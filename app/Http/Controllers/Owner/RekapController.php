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
        $area   = auth()->user()->area;
        $areaId = $area?->id_area;

        $filter = $request->input('filter', 'harian');
        $fa     = $areaId ?? (int) $request->input('area', 0);
        $fj     = $request->input('jenis', '');
        $fsort  = in_array($request->input('sort'), ['waktu_keluar', 'biaya_total'])
                    ? $request->input('sort') : 'waktu_keluar';
        $forder = $request->input('order', 'desc') === 'asc' ? 'asc' : 'desc';

        [$df, $dt, $subLabel] = match($filter) {
            'harian'  => [now()->startOfDay(), now()->addDay()->startOfDay(), 'Menampilkan data hari ini'],
            'mingguan' => [now()->startOfWeek(), now()->addWeek()->startOfWeek(), 'Menampilkan data minggu ini'],
            'bulanan' => [now()->startOfMonth(), now()->addMonth()->startOfMonth(), 'Menampilkan data bulan ini'],
            'tahunan' => [now()->startOfYear(),  now()->addYear()->startOfYear(),  'Menampilkan data tahun ini'],
            'custom'  => [
                Carbon::parse($request->input('dari', now()->startOfMonth()))->startOfDay(),
                Carbon::parse($request->input('sampai', now()->endOfMonth()))->addDay()->startOfDay(),
                Carbon::parse($request->input('dari',    now()->startOfMonth()))->format('d M Y')
                    . ' s/d '
                    . Carbon::parse($request->input('sampai', now()->endOfMonth()))->format('d M Y'),
            ],
            default => [now()->startOfDay(), now()->addDay()->startOfDay(), 'Menampilkan data hari ini'],
        };

        $query = TbTransaksi::with(['kendaraan', 'tarif', 'area'])
            ->where('status', 'keluar')
            ->where('waktu_keluar', '>=', $df)
            ->where('waktu_keluar', '<', $dt)
            ->when($fa, fn($q) => $q->where('id_area', $fa))
            ->when($fj, fn($q) => $q->whereHas('kendaraan', fn($k) => $k->where('jenis_kendaraan', $fj)))
            ->orderBy($fsort, $forder);

        $rekap = $query->paginate(11)->withQueryString();

        if ($request->input('export')) {
            $exportRows = (clone $query)->get();
            // stream as Excel-compatible (xls) — simple CSV with Excel MIME and BOM
            $filename   = 'rekap_transaksi_' . now()->format('Ymd') . '.xls';
            $headers    = [
                'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($exportRows) {
                $f = fopen('php://output', 'w');
                // UTF-8 BOM so Excel opens UTF-8 characters correctly
                fwrite($f, "\xEF\xBB\xBF");
                fputcsv($f, ['ID Transaksi','Tanggal','Plat Nomor','Jenis','Area','Masuk','Keluar','Durasi','Total']);
                foreach ($exportRows as $t) {
                    fputcsv($f, [
                        'TRX-' . str_pad($t->id_parkir, 4, '0', STR_PAD_LEFT),
                        $t->waktu_masuk?->format('d M Y') ?? '-',
                        $t->kendaraan->plat_nomor ?? '-',
                        $t->kendaraan->jenis_kendaraan ? ucfirst($t->kendaraan->jenis_kendaraan) : '-',
                        $t->area->nama_area ?? '-',
                        $t->waktu_masuk?->format('Y-m-d H:i:s') ?? '-',
                        $t->waktu_keluar?->format('Y-m-d H:i:s') ?? '-',
                        $t->durasiLabel ?? '-',
                        $t->biaya_total ?? 0,
                    ]);
                }
                fclose($f);
            };

            return response()->stream($callback, 200, $headers);
        }

        $statsQ = TbTransaksi::where('status', 'keluar')
            ->where('waktu_keluar', '>=', $df)
            ->where('waktu_keluar', '<', $dt)
            ->when($fa, fn($q) => $q->where('id_area', $fa))
            ->when($fj, fn($q) => $q->whereHas('kendaraan', fn($k) => $k->where('jenis_kendaraan', $fj)));

        $totalRev  = (clone $statsQ)->sum('biaya_total');
        $totalKend = (clone $statsQ)->count();
        $avgBiaya  = $totalKend > 0 ? (int) round($totalRev / $totalKend) : 0;

        $sedangParkir = TbTransaksi::where('status', 'masuk')
            ->when($fa, fn($q) => $q->where('id_area', $fa))
            ->count();

        $todayStart = now()->startOfDay();
        $todayEnd   = now()->addDay()->startOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();
        $yesterdayEnd   = now()->startOfDay();

        $revHariIni = TbTransaksi::where('waktu_keluar', '>=', $todayStart)
            ->where('waktu_keluar', '<', $todayEnd)
            ->where('status', 'keluar')
            ->when($fa, fn($q) => $q->where('id_area', $fa))
            ->sum('biaya_total');

        $revKemarin = TbTransaksi::where('waktu_keluar', '>=', $yesterdayStart)
            ->where('waktu_keluar', '<', $yesterdayEnd)
            ->where('status', 'keluar')
            ->when($fa, fn($q) => $q->where('id_area', $fa))
            ->sum('biaya_total');

        $totalArea = TbAreaParkir::count();

        $perArea = TbTransaksi::select('id_area', DB::raw('COUNT(*) as jml'), DB::raw('SUM(biaya_total) as tot'))
            ->where('status', 'keluar')
            ->where('waktu_keluar', '>=', $df)
            ->where('waktu_keluar', '<', $dt)
            ->when($fa, fn($q) => $q->where('id_area', $fa))
            ->when($fj, fn($q) => $q->whereHas('kendaraan', fn($k) => $k->where('jenis_kendaraan', $fj)))
            ->groupBy('id_area')
            ->orderByDesc('tot')
            ->with('area')
            ->get();

        $topArea = $perArea->first()?->area?->nama_area ?? '—';

        $chartRaw = TbTransaksi::select(
                DB::raw('DATE(waktu_keluar) as tgl'),
                DB::raw('SUM(biaya_total) as tot')
            )
            ->where('status', 'keluar')
            ->where('waktu_keluar', '>=', now()->subDays(11)->startOfDay())
            ->where('waktu_keluar', '<', now()->addDay()->startOfDay())
            ->when($fa, fn($q) => $q->where('id_area', $fa))
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->pluck('tot', 'tgl');

        $chart = [];
        for ($i = 11; $i >= 0; $i--) {
            $d       = now()->subDays($i)->format('Y-m-d');
            $chart[] = [
                'date' => $d,
                'day'  => now()->subDays($i)->format('d'),
                'val'  => (float) ($chartRaw[$d] ?? 0),
            ];
        }

        $chartRawPrev = TbTransaksi::select(
                DB::raw('DATE(waktu_keluar) as tgl'),
                DB::raw('SUM(biaya_total) as tot')
            )
            ->where('status', 'keluar')
            ->where('waktu_keluar', '>=', now()->subDays(13)->startOfDay())
            ->where('waktu_keluar', '<', now()->startOfDay())
            ->when($fa, fn($q) => $q->where('id_area', $fa))
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->pluck('tot', 'tgl');

        $chartKemarin = [];
        foreach ($chart as $c) {
            $dprev          = Carbon::parse($c['date'])->subDay()->format('Y-m-d');
            $chartKemarin[] = [
                'date' => $dprev,
                'day'  => Carbon::parse($dprev)->format('d'),
                'val'  => (float) ($chartRawPrev[$dprev] ?? 0),
            ];
        }

        $valsHari = array_column($chart, 'val');
        $valsKem  = array_column($chartKemarin, 'val');

        $allVals  = array_filter(array_merge($valsHari, $valsKem), fn($v) => $v > 0);
        $chartMax = count($allVals) > 0 ? (float) max($allVals) : 1;

        $areaList = TbAreaParkir::orderBy('nama_area')->get();

        TbLogAktivitas::catat(auth()->id(), "Mengakses rekap transaksi periode: {$subLabel}");

        return view('owner.rekap', compact(
            'rekap',
            'filter', 'df', 'dt', 'subLabel',
            'fa', 'fj', 'fsort', 'forder',
            'totalRev', 'totalKend', 'avgBiaya',
            'sedangParkir', 'revHariIni', 'revKemarin', 'totalArea',
            'area', 'areaList',
            'chart', 'chartKemarin', 'chartMax',
            'topArea', 'perArea'
        ));
    }
}