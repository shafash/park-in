<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\TbTransaksi;
use App\Models\TbKendaraan;
use App\Models\TbAreaParkir;
use App\Models\TbTarif;
use App\Services\ParkingCalculator;
use App\Services\ParkingTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\StatsService;

class TransaksiController extends Controller
{
    private StatsService $statsService;
    private ParkingTransactionService $transactionService;

    public function __construct(StatsService $statsService, ParkingTransactionService $transactionService)
    {
        $this->statsService = $statsService;
        $this->transactionService = $transactionService;
    }

    private function stats(): array
    {
        return $this->statsService->petugasStats(Auth::user()->id_area ?? null);
    }

    /**
     * AJAX: cari kendaraan berdasarkan plat nomor
     */
    public function cariPlat(Request $request)
    {
        $q = preg_replace('/[^A-Z0-9]/', '', strtoupper(trim($request->input('q', ''))));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // OPTIMIZED QUERY: prefix search instead of wildcard
        $results = TbKendaraan::where('plat_nomor', 'like', "{$q}%")
            ->limit(8)
            ->get(['id_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'merek', 'warna', 'pemilik', 'foto'])
            ->map(function ($k) {
                $tarifId = TbTarif::where('jenis_kendaraan', $k->jenis_kendaraan)->value('id_tarif');

                return [
                    'id_kendaraan'    => $k->id_kendaraan,
                    'plat_nomor'      => $k->plat_nomor,
                    'jenis_kendaraan' => $k->jenis_kendaraan,
                    'jenis_label'     => $k->jenisLabel,
                    'merek'           => $k->merek,
                    'warna'           => $k->warna,
                    'pemilik'         => $k->pemilik,
                    'foto_url'        => $k->fotoUrl,
                    'id_tarif_match'  => $tarifId,
                ];
            });

        return response()->json($results);
    }

    public function index(Request $request)
    {
        $q      = $request->input('q', '');
        $status = $request->input('status', '');
        $jenis  = $request->input('jenis', '');
        $sort   = in_array($request->input('sort'), ['waktu_masuk', 'biaya_total']) ? $request->input('sort') : 'waktu_masuk';
        $order  = $request->input('order', 'desc') === 'asc' ? 'asc' : 'desc';

        $userArea = Auth::user()->id_area ?? null;

        $query = TbTransaksi::with(['kendaraan', 'tarif', 'area'])
            ->when($q,      fn($q2) => $q2->whereHas('kendaraan', fn($k) => $k->where('plat_nomor', 'like', "{$q}%")))
            ->when($status, fn($q2) => $q2->where('status', $status))
            ->when($jenis,  fn($q2) => $q2->whereHas('kendaraan', fn($k) => $k->where('jenis_kendaraan', $jenis)))
            ->when($userArea, fn($q2) => $q2->where('id_area', $userArea))
            ->orderBy($sort, $order);

        $transaksis = $query->paginate(9)->withQueryString();

        $jenisList = TbTarif::orderBy('jenis_kendaraan')->pluck('jenis_kendaraan')->filter()->unique()->values()->all();
        if (empty($jenisList)) {
            $jenisList = TbKendaraan::distinct()->orderBy('jenis_kendaraan')->pluck('jenis_kendaraan')->filter()->unique()->values()->all();
        }

        $colorPool = ['p-grn', 'p-blu', 'p-ora'];
        $jenisColors = [];
        foreach ($jenisList as $i => $j) {
            $jenisColors[$j] = $colorPool[$i % count($colorPool)];
        }

        return view('petugas.transaksi', array_merge($this->stats(), compact('transaksis', 'q', 'status', 'jenis', 'sort', 'order', 'jenisList', 'jenisColors')));
    }

    public function masukForm()
    {
        $areas  = TbAreaParkir::where('status', 1)->whereRaw('terisi < kapasitas')->orderBy('nama_area')->get();
        $tarifs = TbTarif::orderBy('jenis_kendaraan')->get();
        $allAreas = TbAreaParkir::orderBy('nama_area')->get();
        $area = Auth::user()->area ?? null;
        $userArea = Auth::user()->id_area ?? null;

        $liveFeed = TbTransaksi::with(['kendaraan', 'tarif', 'area'])
            ->when($userArea, fn($q) => $q->where('id_area', $userArea))
            ->where(function($q) {
                $q->whereDate('waktu_masuk', today())
                  ->orWhereDate('waktu_keluar', today());
            })
            ->orderByDesc('waktu_masuk')
            ->limit(3)
            ->get();

        return view('petugas.masuk', array_merge($this->stats(), compact('areas', 'tarifs', 'allAreas', 'area', 'liveFeed')));
    }

    public function masukStore(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'id_tarif'   => 'required|exists:tb_tarif,id_tarif',
            'id_area'    => 'required|exists:tb_area_parkir,id_area',
        ]);

        $plat = preg_replace('/[^A-Z0-9]/', '', strtoupper($request->plat_nomor));
        if (Auth::user()->id_area && Auth::user()->id_area != $request->id_area) {
            return back()->with('error', 'Anda tidak berwenang mengakses area tersebut.')->withInput();
        }

        $ikey = $request->input('idempotency_key');
        $cacheKey = $ikey ? "idempotency:masuk:{$ikey}" : null;

        if ($cacheKey && !Cache::add($cacheKey, true, now()->addMinutes(5))) {
            return back()->with('error', 'Permintaan sedang diproses atau sudah diproses.')->withInput();
        }

        try {
            $this->transactionService->masuk($plat, $request->id_tarif, $request->id_area, Auth::id());
            return back()->with('success', "Kendaraan $plat berhasil dicatat masuk.");
        } catch (\Exception $e) {
            if ($cacheKey) Cache::forget($cacheKey);
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            if ($cacheKey) Cache::forget($cacheKey);
            throw $e;
        }
    }

    public function keluarForm($id)
    {
        $est = $this->transactionService->estimasiKeluar($id);
        $trx = $est['trx'];

        if (Auth::user()->id_area && $trx->id_area != Auth::user()->id_area) {
            return back()->with('error', 'Anda tidak berwenang melihat transaksi di area ini.');
        }

        return view('petugas.keluar', array_merge($this->stats(), ['trx' => $trx, 'durEst' => $est['durasiJam'], 'estBiaya' => $est['estBiaya']]));
    }

    public function keluarStore(Request $request, $id)
    {
        $ikey = $request->input('idempotency_key');
        $cacheKey = $ikey ? "idempotency:keluar:{$ikey}" : null;

        if ($cacheKey && !Cache::add($cacheKey, true, now()->addMinutes(5))) {
            return redirect()->route('petugas.struk.show', $id)
                ->with('success', 'Transaksi sedang diproses atau sudah diproses.');
        }

        try {
            $result = $this->transactionService->keluar($id, Auth::id(), Auth::user()->id_area);
            
            session(['last_trx' => $id]);
            return redirect()->route('petugas.struk.show', $id)
                ->with('success', "Kendaraan {$result['trx']->kendaraan->plat_nomor} selesai. Biaya: Rp " . number_format($result['biaya'], 0, ',', '.'));
        } catch (\Exception $e) {
            if ($cacheKey) Cache::forget($cacheKey);
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            if ($cacheKey) Cache::forget($cacheKey);
            throw $e;
        }
    }
}
