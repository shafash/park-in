<?php

namespace App\Services;

use App\Models\TbTransaksi;
use App\Models\User;
use App\Models\TbAreaParkir;
use App\Models\TbKendaraan;
use App\Models\TbLogAktivitas;
use App\Models\TbTarif;

class StatsService
{
    /**
     * Stats for petugas dashboards (filtered by optional area)
     * Returns keys: masuk, keluar, diarea, struk
     */
    public function petugasStats(?int $userArea = null): array
    {
        return [
            'masuk'  => TbTransaksi::when($userArea, fn($q) => $q->where('id_area', $userArea))->whereDate('waktu_masuk', today())->count(),
            'keluar' => TbTransaksi::when($userArea, fn($q) => $q->where('id_area', $userArea))->whereDate('waktu_masuk', today())->where('status', 'keluar')->count(),
            'diarea' => TbTransaksi::when($userArea, fn($q) => $q->where('id_area', $userArea))->where('status', 'masuk')->count(),
            'struk'  => TbTransaksi::when($userArea, fn($q) => $q->where('id_area', $userArea))->whereDate('waktu_masuk', today())->where('status', 'keluar')->count(),
        ];
    }

    /**
     * Common admin stats used across admin controllers
     * Returns keys: total_user, area_aktif, total_kendaraan, log_hari
     */
    public function adminStats(): array
    {
        return [
            'total_user'      => User::count(),
            'area_aktif'      => TbAreaParkir::where('status', 1)->count(),
            'total_kendaraan' => TbKendaraan::count(),
            'log_hari'        => TbLogAktivitas::whereDate('waktu_aktivitas', today())->count(),
        ];
    }

    /**
     * Tarif page stats: adminStats + jenis_tarif
     */
    public function tarifStats(): array
    {
        $s = $this->adminStats();
        $s['jenis_tarif'] = TbTarif::count();
        return $s;
    }
}
