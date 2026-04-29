<?php

namespace Tests\Feature;

use App\Models\TbAreaParkir;
use App\Models\TbKendaraan;
use App\Models\TbTarif;
use App\Models\TbTransaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;
    private TbAreaParkir $area;
    private TbTarif $tarif;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = TbAreaParkir::create([
            'nama_area' => 'Area A',
            'alamat' => 'Lantai 1',
            'kapasitas' => 10,
            'terisi' => 0,
            'status' => 1,
        ]);

        $this->tarif = TbTarif::create([
            'jenis_kendaraan' => 'motor',
            'tarif_awal' => 2000,
            'tarif_per_jam' => 3000,
            'denda_per_jam' => 5000,
            'batas_durasi_jam' => 8,
        ]);

        $this->petugas = User::create([
            'nama_lengkap' => 'Petugas Test',
            'username' => 'petugas_test',
            'password' => 'password123',
            'role' => 'petugas',
            'status_aktif' => 1,
            'id_area' => $this->area->id_area,
        ]);
    }

    public function test_transaksi_masuk_berhasil_disimpan(): void
    {
        $this->actingAs($this->petugas)
            ->post(route('petugas.transaksi.masuk.store'), [
                'plat_nomor' => 'B 1234 ABC',
                'id_tarif' => $this->tarif->id_tarif,
                'id_area' => $this->area->id_area,
            ])
            ->assertRedirect();

        $kendaraan = TbKendaraan::where('plat_nomor', 'B 1234 ABC')->first();
        $this->assertNotNull($kendaraan);

        $this->assertDatabaseHas('tb_transaksi', [
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'id_tarif' => $this->tarif->id_tarif,
            'id_area' => $this->area->id_area,
            'status' => 'masuk',
        ]);

        $this->assertDatabaseHas('tb_area_parkir', [
            'id_area' => $this->area->id_area,
            'terisi' => 1,
        ]);
    }

    public function test_transaksi_keluar_mengubah_status_dan_kapasitas(): void
    {
        $kendaraan = TbKendaraan::create([
            'plat_nomor' => 'B 9999 XYZ',
            'jenis_kendaraan' => 'motor',
            'merek' => 'Honda',
            'warna' => 'Hitam',
            'pemilik' => 'User Test',
            'created_at' => now(),
        ]);

        $trx = TbTransaksi::create([
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'waktu_masuk' => now()->subHours(3),
            'id_tarif' => $this->tarif->id_tarif,
            'status' => 'masuk',
            'id_user' => $this->petugas->id_user,
            'id_area' => $this->area->id_area,
        ]);

        $this->area->update(['terisi' => 1]);

        $this->actingAs($this->petugas)
            ->post(route('petugas.transaksi.keluar.store', $trx->id_parkir))
            ->assertRedirect(route('petugas.struk.show', $trx->id_parkir));

        $trx->refresh();
        $this->assertSame('keluar', $trx->status);
        $this->assertNotNull($trx->waktu_keluar);

        $this->assertDatabaseHas('tb_area_parkir', [
            'id_area' => $this->area->id_area,
            'terisi' => 0,
        ]);
    }

    public function test_transaksi_tidak_bisa_keluar_dua_kali(): void
    {
        $kendaraan = TbKendaraan::create([
            'plat_nomor' => 'B 7777 TST',
            'jenis_kendaraan' => 'motor',
            'merek' => 'Yamaha',
            'warna' => 'Biru',
            'pemilik' => 'QA',
            'created_at' => now(),
        ]);

        $trx = TbTransaksi::create([
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'waktu_masuk' => now()->subHours(2),
            'id_tarif' => $this->tarif->id_tarif,
            'status' => 'masuk',
            'id_user' => $this->petugas->id_user,
            'id_area' => $this->area->id_area,
        ]);

        $this->area->update(['terisi' => 1]);

        $this->actingAs($this->petugas)
            ->post(route('petugas.transaksi.keluar.store', $trx->id_parkir))
            ->assertRedirect(route('petugas.struk.show', $trx->id_parkir));

        $this->actingAs($this->petugas)
            ->post(route('petugas.transaksi.keluar.store', $trx->id_parkir))
            ->assertRedirect(route('petugas.struk.show', $trx->id_parkir));

        $trx->refresh();
        $this->assertSame('keluar', $trx->status);

        $this->assertDatabaseHas('tb_area_parkir', [
            'id_area' => $this->area->id_area,
            'terisi' => 0,
        ]);
    }
}
