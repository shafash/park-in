<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\TbTransaksi;
use App\Services\ParkingCalculator;
use App\Models\TbKendaraan;
use App\Models\TbAreaParkir;
use App\Models\TbTarif;
use App\Models\TbLogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    private function stats(): array
    {
        $userArea = Auth::user()->id_area ?? null;

        return [
            'masuk'  => TbTransaksi::when($userArea, fn($q) => $q->where('id_area', $userArea))->whereDate('waktu_masuk', today())->count(),
            'keluar' => TbTransaksi::when($userArea, fn($q) => $q->where('id_area', $userArea))->whereDate('waktu_masuk', today())->where('status', 'keluar')->count(),
            'diarea' => TbTransaksi::when($userArea, fn($q) => $q->where('id_area', $userArea))->where('status', 'masuk')->count(),
            'struk'  => TbTransaksi::when($userArea, fn($q) => $q->where('id_area', $userArea))->whereDate('waktu_masuk', today())->where('status', 'keluar')->count(),
        ];
    }

    /**
     * AJAX: cari kendaraan berdasarkan plat nomor
     * Dipanggil saat petugas ketik plat di form masuk
     */
    public function cariPlat(Request $request)
    {
        $q = strtoupper(trim($request->input('q', '')));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = \App\Models\TbKendaraan::where('plat_nomor', 'like', "%{$q}%")
            ->limit(8)
            ->get(['id_kendaraan', 'plat_nomor', 'jenis_kendaraan', 'merek', 'warna', 'pemilik', 'foto'])
            ->map(function ($k) {
                $tarifId = \App\Models\TbTarif::where('jenis_kendaraan', $k->jenis_kendaraan)->value('id_tarif');

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
            ->when($q,      fn($q2) => $q2->whereHas('kendaraan', fn($k) => $k->where('plat_nomor', 'like', "%{$q}%")))
            ->when($status, fn($q2) => $q2->where('status', $status))
            ->when($jenis,  fn($q2) => $q2->whereHas('kendaraan', fn($k) => $k->where('jenis_kendaraan', $jenis)))
            ->when($userArea, fn($q2) => $q2->where('id_area', $userArea))
            ->orderBy($sort, $order);

        $transaksis = $query->paginate(9)->withQueryString();

        // Build dynamic list of jenis kendaraan from DB (prefer tariffs, fallback to kendaraan)
        $jenisList = TbTarif::orderBy('jenis_kendaraan')->pluck('jenis_kendaraan')->filter()->unique()->values()->all();
        if (empty($jenisList)) {
            $jenisList = TbKendaraan::distinct()->orderBy('jenis_kendaraan')->pluck('jenis_kendaraan')->filter()->unique()->values()->all();
        }

        // Assign rotating color classes to each jenis so they don't all look the same
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

        // Jika petugas/admin punya area tetap, kirimkan juga sebagai `area`
        $area = Auth::user()->area ?? null;

        // Live feed: aktivitas hari ini (masuk/keluar) — batasi ke area user jika ditetapkan
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

        $plat = strtoupper($request->plat_nomor);
        // Jika user punya area yang ditetapkan, pastikan tidak boleh pilih area lain
        if (Auth::user()->id_area && Auth::user()->id_area != $request->id_area) {
            return back()->with('error', 'Anda tidak berwenang mengakses area tersebut.')->withInput();
        }

        return DB::transaction(function () use ($request, $plat) {
            $area = TbAreaParkir::where('id_area', $request->id_area)
                ->lockForUpdate()
                ->firstOrFail();

            if ($area->terisi >= $area->kapasitas) {
                return back()->with('error', "Area {$area->nama_area} sudah penuh!")->withInput();
            }

            $kendaraan = TbKendaraan::where('plat_nomor', $plat)->lockForUpdate()->first();
            if (!$kendaraan) {
                $tarif = TbTarif::find($request->id_tarif);
                $kendaraan = TbKendaraan::create([
                    'plat_nomor'      => $plat,
                    'jenis_kendaraan' => $tarif->jenis_kendaraan ?? 'lainnya',
                    'warna'           => '',
                    'pemilik'         => '',
                    'created_at'      => now(),
                ]);

                $kendaraan = TbKendaraan::where('id_kendaraan', $kendaraan->id_kendaraan)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $aktif = TbTransaksi::where('id_kendaraan', $kendaraan->id_kendaraan)
                ->where('status', 'masuk')
                ->lockForUpdate()
                ->first();

            // Idempotency dasar: request ulang untuk kendaraan yang sama tidak membuat transaksi ganda.
            if ($aktif) {
                return back()->with('error', "Kendaraan $plat masih aktif parkir.")->withInput();
            }

            TbTransaksi::create([
                'id_kendaraan' => $kendaraan->id_kendaraan,
                'waktu_masuk'  => now(),
                'id_tarif'     => $request->id_tarif,
                'status'       => 'masuk',
                'id_user'      => Auth::id(),
                'id_area'      => $request->id_area,
            ]);

            $area->increment('terisi');
            TbLogAktivitas::catat(Auth::id(), "Kendaraan masuk: $plat ke {$area->nama_area}");

            return back()->with('success', "Kendaraan $plat berhasil dicatat masuk ke {$area->nama_area}.");
        });
    }

    public function keluarForm($id)
    {
        $trx = TbTransaksi::with(['kendaraan', 'tarif', 'area'])
            ->where('id_parkir', $id)
            ->where('status', 'masuk')
            ->firstOrFail();

        // Batasi operasi keluar ke area milik user ketika ditetapkan
        if (Auth::user()->id_area && $trx->id_area != Auth::user()->id_area) {
            return back()->with('error', 'Anda tidak berwenang melihat transaksi di area ini.');
        }

        $durEst = max(1, (int) ceil((now()->timestamp - $trx->waktu_masuk->timestamp) / 3600));
        $basePrice   = $trx->tarif->tarif_awal ?? 0;
        $hourlyRate  = $trx->tarif->tarif_per_jam ?? 0;
        $maxHours    = $trx->tarif->batas_durasi_jam ?? 0;
        $penaltyRate = $trx->tarif->denda_per_jam ?? 0;

        $estBiaya = ParkingCalculator::calculateFromMinutes(
            durationMinutes: $durEst * 60,
            basePrice: $basePrice,
            hourlyRate: $hourlyRate,
            maxHours: $maxHours,
            penaltyRate: $penaltyRate
        );

        return view('petugas.keluar', array_merge($this->stats(), compact('trx', 'durEst', 'estBiaya')));
    }

    public function keluarStore(Request $request, $id)
    {
        return DB::transaction(function () use ($id) {
            $trx = TbTransaksi::with(['kendaraan', 'tarif'])
                ->where('id_parkir', $id)
                ->lockForUpdate()
                ->firstOrFail();

            // Batasi operasi keluar ke area milik user ketika ditetapkan
            if (Auth::user()->id_area && $trx->id_area != Auth::user()->id_area) {
                return back()->with('error', 'Anda tidak berwenang memproses transaksi di area ini.');
            }

            $area = TbAreaParkir::where('id_area', $trx->id_area)
                ->lockForUpdate()
                ->firstOrFail();

            // Idempotency dasar: request keluar berulang pada transaksi yang sama tidak diproses ulang.
            if ($trx->status !== 'masuk') {
                return redirect()->route('petugas.struk.show', $id)
                    ->with('success', "Transaksi {$trx->kendaraan->plat_nomor} sudah diproses sebelumnya.");
            }

            $wk  = now();
            $dur = max(1, (int) ceil(($wk->timestamp - $trx->waktu_masuk->timestamp) / 3600));

            $basePrice   = $trx->tarif->tarif_awal ?? 0;
            $hourlyRate  = $trx->tarif->tarif_per_jam ?? 0;
            $maxHours    = $trx->tarif->batas_durasi_jam ?? 0;
            $penaltyRate = $trx->tarif->denda_per_jam ?? 0;

            $biaya = ParkingCalculator::calculateFromMinutes(
                durationMinutes: $dur * 60,
                basePrice: $basePrice,
                hourlyRate: $hourlyRate,
                maxHours: $maxHours,
                penaltyRate: $penaltyRate
            );

            $trx->update([
                'waktu_keluar' => $wk,
                'durasi_jam'   => $dur,
                'biaya_total'  => $biaya,
                'status'       => 'keluar',
            ]);

            if ($area->terisi > 0) {
                $area->decrement('terisi');
            }

            TbLogAktivitas::catat(Auth::id(), "Kendaraan keluar: {$trx->kendaraan->plat_nomor} — {$area->nama_area} — Rp " . number_format($biaya, 0, ',', '.'));

            session(['last_trx' => $id]);

            return redirect()->route('petugas.struk.show', $id)
                ->with('success', "Kendaraan {$trx->kendaraan->plat_nomor} selesai. Biaya: Rp " . number_format($biaya, 0, ',', '.'));
        });
    }
}
