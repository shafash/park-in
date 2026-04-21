<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\TbTarif;
use App\Models\TbKendaraan;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Use simple pagination (kita pakai custom pager di Blade)
        Paginator::useBootstrapFive();
        
        // Share dynamic jenis list and consistent color mapping across all views
        try {
            $jenisList = TbTarif::orderBy('jenis_kendaraan')->pluck('jenis_kendaraan')->filter()->unique()->values()->all();
            if (empty($jenisList)) {
                $jenisList = TbKendaraan::distinct()->orderBy('jenis_kendaraan')->pluck('jenis_kendaraan')->filter()->unique()->values()->all();
            }

            $colorPool = ['p-grn', 'p-blu', 'p-ora', 'p-pur', 'p-red'];
            $jenisColors = [];
            foreach ($jenisList as $i => $j) {
                $jenisColors[$j] = $colorPool[$i % count($colorPool)];
            }

            View::share('jenisList', $jenisList);
            View::share('jenisColors', $jenisColors);
        } catch (\Throwable $e) {
        }
    }
}