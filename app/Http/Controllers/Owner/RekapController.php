<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\TbTransaksi;
use App\Models\TbAreaParkir;
use App\Models\TbLogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $filter  = $request->input('filter', 'harian');
        $fa      = $request->input('area', 0);
        $fj      = $request->input('jenis', '');
        $fsort   = in_array($request->input('sort'), ['waktu_masuk', 'biaya_total']) ? $request->input('sort') : 'waktu_masuk';
        $forder  = $request->input('order', 'desc') === 'asc' ? 'asc' : 'desc';

        // Tentukan rentang tanggal
        [$df, $dt, $subLabel] = match($filter) {
            'harian'  => [today(), today(), 'Menampilkan data hari ini'],
            'bulanan' => [now()->startOfMonth(), now()->endOfMonth(), 'Menampilkan data bulan ini'],
            'tahunan' => [now()->startOfYear(), now()->endOfYear(), 'Menampilkan data tahun ini'],
            'custom'  => [
                Carbon::parse($request->input('dari', now()->startOfMonth())),
                Carbon::parse($request->input('sampai', now()->endOfMonth())),
                Carbon::parse($request->input('dari', now()->startOfMonth()))->format('d M Y') . ' s/d ' . Carbon::parse($request->input('sampai', now()->endOfMonth()))->format('d M Y'),
            ],
            default   => [today(), today(), 'Menampilkan data hari ini'],
        };

        // Query utama
        $query = TbTransaksi::with(['kendaraan', 'tarif', 'area'])
            ->whereBetween(DB::raw('DATE(waktu_masuk)'), [$df->format('Y-m-d'), $dt->format('Y-m-d')])
            ->where('status', 'keluar')
            ->when($fa,  fn($q) => $q->where('id_area', $fa))
            ->when($fj,  fn($q) => $q->whereHas('kendaraan', fn($k) => $k->where('jenis_kendaraan', $fj)))
            ->orderBy($fsort, $forder);

        $rekap = $query->paginate(11)->withQueryString();

        // Stats
        $statsQuery = TbTransaksi::with('kendaraan')
            ->whereBetween(DB::raw('DATE(waktu_masuk)'), [$df->format('Y-m-d'), $dt->format('Y-m-d')])
            ->where('status', 'keluar')
            ->when($fa, fn($q) => $q->where('id_area', $fa))
            ->when($fj, fn($q) => $q->whereHas('kendaraan', fn($k) => $k->where('jenis_kendaraan', $fj)));

        $totalRev  = $statsQuery->sum('biaya_total');
        $totalKend = $statsQuery->count();
        $avgBiaya  = $totalKend > 0 ? round($totalRev / $totalKend) : 0;

        // Per area
        $perArea = TbTransaksi::select('id_area', DB::raw('COUNT(*) as jml'), DB::raw('SUM(biaya_total) as tot'))
            ->whereBetween(DB::raw('DATE(waktu_masuk)'), [$df->format('Y-m-d'), $dt->format('Y-m-d')])
            ->where('status', 'keluar')
            ->when($fa, fn($q) => $q->where('id_area', $fa))
            ->when($fj, fn($q) => $q->whereHas('kendaraan', fn($k) => $k->where('jenis_kendaraan', $fj)))
            ->groupBy('id_area')
            ->orderByDesc('tot')
            ->with('area')
            ->get();

        // Chart — 12 hari terakhir
        $chartData = TbTransaksi::select(DB::raw('DATE(waktu_masuk) as tgl'), DB::raw('SUM(biaya_total) as tot'))
            ->whereBetween(DB::raw('DATE(waktu_masuk)'), [now()->subDays(11)->format('Y-m-d'), now()->format('Y-m-d')])
            ->where('status', 'keluar')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->pluck('tot', 'tgl');

        $chart = [];
        for ($i = 11; $i >= 0; $i--) {
            $d       = now()->subDays($i)->format('Y-m-d');
            $chart[] = ['date' => $d, 'day' => now()->subDays($i)->format('d'), 'val' => $chartData[$d] ?? 0];
        }
        $chartMax = max(1, max(array_column($chart, 'val')));

        $topArea  = $perArea->first()->area->nama_area ?? '—';
        $areaList = TbAreaParkir::orderBy('nama_area')->get();

        TbLogAktivitas::catat(auth()->id(), "Mengakses rekap transaksi periode: {$subLabel}");

        return view('owner.rekap', compact(
            'rekap', 'filter', 'df', 'dt', 'subLabel',
            'fa', 'fj', 'fsort', 'forder',
            'totalRev', 'totalKend', 'avgBiaya', 'topArea',
            'perArea', 'chart', 'chartMax', 'areaList'
        ));
    }
}
