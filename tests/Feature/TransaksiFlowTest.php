<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\TbAreaParkir;
use App\Models\TbTarif;
use App\Models\TbTransaksi;
use App\Models\TbKendaraan;

class TransaksiFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_kendaraan_bisa_masuk_jika_area_tersedia()
    {
        $user = User::factory()->create(['role' => 'petugas']);
        $area = TbAreaParkir::create(['nama_area' => 'A1', 'kapasitas' => 10, 'terisi' => 0, 'status' => 1]);
        $tarif = TbTarif::create(['jenis_kendaraan' => 'mobil', 'tarif_awal' => 5000, 'tarif_per_jam' => 3000]);

        $response = $this->actingAs($user)->post('/petugas/transaksi/masuk', [
            'plat_nomor' => 'B1234XYZ',
            'id_tarif' => $tarif->id_tarif,
            'id_area' => $area->id_area
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tb_transaksi', [
            'status' => 'masuk'
        ]);
        $this->assertDatabaseHas('tb_kendaraan', [
            'plat_nomor' => 'B1234XYZ'
        ]);
    }

    public function test_kendaraan_tidak_bisa_masuk_lagi_jika_belum_keluar()
    {
        $user = User::factory()->create(['role' => 'petugas']);
        $area = TbAreaParkir::create(['nama_area' => 'A1', 'kapasitas' => 10, 'terisi' => 0, 'status' => 1]);
        $tarif = TbTarif::create(['jenis_kendaraan' => 'mobil', 'tarif_awal' => 5000]);

        $this->actingAs($user)->post('/petugas/transaksi/masuk', [
            'plat_nomor' => 'B1234XYZ',
            'id_tarif' => $tarif->id_tarif,
            'id_area' => $area->id_area
        ]);

        // Attempt second entry
        $response = $this->actingAs($user)->post('/petugas/transaksi/masuk', [
            'plat_nomor' => 'B1234XYZ',
            'id_tarif' => $tarif->id_tarif,
            'id_area' => $area->id_area
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('tb_transaksi', 1);
    }

    public function test_kendaraan_bisa_keluar_dan_dihitung_biayanya()
    {
        $user = User::factory()->create(['role' => 'petugas']);
        $area = TbAreaParkir::create(['nama_area' => 'A1', 'kapasitas' => 10, 'terisi' => 1, 'status' => 1]);
        $tarif = TbTarif::create(['jenis_kendaraan' => 'mobil', 'tarif_awal' => 5000, 'tarif_per_jam' => 3000]);
        $kendaraan = TbKendaraan::create(['plat_nomor' => 'B9999ZZ', 'jenis_kendaraan' => 'mobil']);
        
        $trx = TbTransaksi::create([
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'id_tarif' => $tarif->id_tarif,
            'id_area' => $area->id_area,
            'waktu_masuk' => now()->subMinutes(60), // parked for 60 mins
            'status' => 'masuk',
            'id_user' => $user->id_user
        ]);

        $response = $this->actingAs($user)->post("/petugas/transaksi/keluar/{$trx->id_parkir}");

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('tb_transaksi', [
            'id_parkir' => $trx->id_parkir,
            'status' => 'keluar',
            'biaya_total' => 5000 // Only base price
        ]);
        
        $this->assertDatabaseHas('tb_area_parkir', [
            'id_area' => $area->id_area,
            'terisi' => 0
        ]);
    }
}
