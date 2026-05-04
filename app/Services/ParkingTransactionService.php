<?php

namespace App\Services;

use App\Models\TbTransaksi;
use App\Models\TbKendaraan;
use App\Models\TbAreaParkir;
use App\Models\TbLogAktivitas;
use Illuminate\Support\Facades\DB;
use App\Services\ParkingCalculator;

class ParkingTransactionService
{
    /**
     * Memproses kendaraan masuk ke area parkir.
     */
    public function masuk(string $plat, int $id_tarif, int $id_area, int $id_user)
    {
        return DB::transaction(function () use ($plat, $id_tarif, $id_area, $id_user) {
            $area = TbAreaParkir::where('id_area', $id_area)
                ->lockForUpdate()
                ->firstOrFail();

            if ($area->terisi >= $area->kapasitas) {
                throw new \Exception("Area {$area->nama_area} sudah penuh!");
            }

            $kendaraan = TbKendaraan::where('plat_nomor', $plat)->lockForUpdate()->first();
            if (!$kendaraan) {
                $tarif = \App\Models\TbTarif::find($id_tarif);
                $kendaraan = TbKendaraan::create([
                    'plat_nomor'      => $plat,
                    'jenis_kendaraan' => $tarif->jenis_kendaraan ?? 'lainnya',
                    'warna'           => '',
                    'pemilik'         => '',
                    'created_at'      => now(),
                ]);
            }

            $aktif = TbTransaksi::where('id_kendaraan', $kendaraan->id_kendaraan)
                ->where('status', 'masuk')
                ->lockForUpdate()
                ->first();

            if ($aktif) {
                throw new \Exception("Kendaraan $plat masih aktif parkir. Selesaikan transaksi lama terlebih dahulu.");
            }

            $trx = TbTransaksi::create([
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'waktu_masuk'  => now(),
                'id_tarif'     => $id_tarif,
                'status'       => 'masuk',
                'id_user'      => $id_user,
                'id_area'      => $id_area,
            ]);

            $area->increment('terisi');
            TbLogAktivitas::catat($id_user, "Kendaraan masuk: $plat ke {$area->nama_area}");

            return $trx;
        });
    }

    /**
     * Memproses kendaraan keluar dari area parkir.
     */
    public function keluar(int $id_parkir, int $id_user, ?int $user_area = null)
    {
        return DB::transaction(function () use ($id_parkir, $id_user, $user_area) {
            $trx = TbTransaksi::with(['kendaraan', 'tarif'])
                ->where('id_parkir', $id_parkir)
                ->lockForUpdate()
                ->firstOrFail();

            if ($user_area && $trx->id_area != $user_area) {
                throw new \Exception('Anda tidak berwenang memproses transaksi di area ini.');
            }

            $area = TbAreaParkir::where('id_area', $trx->id_area)
                ->lockForUpdate()
                ->firstOrFail();

            if ($trx->status !== 'masuk') {
                throw new \Exception("Transaksi {$trx->kendaraan->plat_nomor} sudah diproses sebelumnya.");
            }

            $waktu_keluar = now();
            // Kalkulasi selisih dalam murni MENIT
            $durasiMenit = max(1, (int) round(($waktu_keluar->timestamp - $trx->waktu_masuk->timestamp) / 60));

            $basePrice   = $trx->tarif->tarif_awal ?? 0;
            $hourlyRate  = $trx->tarif->tarif_per_jam ?? 0;
            $maxHours    = $trx->tarif->batas_durasi_jam ?? 0;
            $penaltyRate = $trx->tarif->denda_per_jam ?? 0;

            // Panggil Service kalkulator menggunakan Menit
            $biaya = ParkingCalculator::calculateFromMinutes(
                $durasiMenit,
                $basePrice,
                $hourlyRate,
                $maxHours,
                $penaltyRate
            );

            // Perkiraan jam untuk display struk (pembulatan wajar)
            $durasiJam = (int) ceil(max(0, $durasiMenit - 15) / 60);
            if ($durasiJam < 1) $durasiJam = 1;

            $trx->update([
                'waktu_keluar' => $waktu_keluar,
                'durasi_jam'   => $durasiJam,
                'biaya_total'  => $biaya,
                'status'       => 'keluar',
            ]);

            if ($area->terisi > 0) {
                $area->decrement('terisi');
            }

            TbLogAktivitas::catat($id_user, "Kendaraan keluar: {$trx->kendaraan->plat_nomor} — {$area->nama_area} — Rp " . number_format($biaya, 0, ',', '.'));

            return [
                'trx' => $trx,
                'biaya' => $biaya
            ];
        });
    }
}
