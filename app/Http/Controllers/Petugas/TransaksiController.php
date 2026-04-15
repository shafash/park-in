<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\TbTransaksi;
use App\Models\TbKendaraan;
use App\Models\TbAreaParkir;
use App\Models\TbTarif;
use App\Models\TbLogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    private function stats(): array
    {
        return [
            'masuk'  => TbTransaksi::whereDate('waktu_masuk', today())->count(),
            'keluar' => TbTransaksi::whereDate('waktu_masuk', today())->where('status', 'keluar')->count(),
            'diarea' => TbTransaksi::where('status', 'masuk')->count(),
            'struk'  => TbTransaksi::whereDate('waktu_masuk', today())->where('status', 'keluar')->count(),
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
                return [
                    'id_kendaraan'    => $k->id_kendaraan,
                    'plat_nomor'      => $k->plat_nomor,
                    'jenis_kendaraan' => $k->jenis_kendaraan,
                    'jenis_label'     => $k->jenisLabel,
                    'merek'           => $k->merek,
                    'warna'           => $k->warna,
                    'pemilik'         => $k->pemilik,
                    'foto_url'        => $k->fotoUrl,
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

        $query = TbTransaksi::with(['kendaraan', 'tarif', 'area'])
            ->when($q,      fn($q2) => $q2->whereHas('kendaraan', fn($k) => $k->where('plat_nomor', 'like', "%{$q}%")))
            ->when($status, fn($q2) => $q2->where('status', $status))
            ->when($jenis,  fn($q2) => $q2->whereHas('kendaraan', fn($k) => $k->where('jenis_kendaraan', $jenis)))
            ->orderBy($sort, $order);

        $transaksis = $query->paginate(9)->withQueryString();

        return view('petugas.transaksi', array_merge($this->stats(), compact('transaksis', 'q', 'status', 'jenis', 'sort', 'order')));
    }

    public function masukForm()
    {
        $areas  = TbAreaParkir::where('status', 1)->whereRaw('terisi < kapasitas')->orderBy('nama_area')->get();
        $tarifs = TbTarif::orderBy('jenis_kendaraan')->get();
        $allAreas = TbAreaParkir::orderBy('nama_area')->get();

        return view('petugas.masuk', array_merge($this->stats(), compact('areas', 'tarifs', 'allAreas')));
    }

    public function masukStore(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'id_tarif'   => 'required|exists:tb_tarif,id_tarif',
            'id_area'    => 'required|exists:tb_area_parkir,id_area',
        ]);

        $plat = strtoupper($request->plat_nomor);

        // Cek / daftar kendaraan
        $kendaraan = TbKendaraan::firstOrCreate(
            ['plat_nomor' => $plat],
            [
                'jenis_kendaraan' => TbTarif::find($request->id_tarif)->jenis_kendaraan ?? 'lainnya',
                'warna'           => '',
                'pemilik'         => '',
                'created_at'      => now(),
            ]
        );

        // Cek masih aktif
        if (TbTransaksi::where('id_kendaraan', $kendaraan->id_kendaraan)->where('status', 'masuk')->exists()) {
            return back()->with('error', "Kendaraan $plat masih aktif parkir.")->withInput();
        }

        $area = TbAreaParkir::findOrFail($request->id_area);
        if ($area->terisi >= $area->kapasitas) {
            return back()->with('error', "Area {$area->nama_area} sudah penuh!")->withInput();
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
    }

    public function keluarForm($id)
    {
        $trx = TbTransaksi::with(['kendaraan', 'tarif', 'area'])
            ->where('id_parkir', $id)
            ->where('status', 'masuk')
            ->firstOrFail();

        $durEst = max(1, (int) ceil((now()->timestamp - $trx->waktu_masuk->timestamp) / 3600));
        $estBiaya = $durEst * $trx->tarif->tarif_per_jam;

        return view('petugas.keluar', array_merge($this->stats(), compact('trx', 'durEst', 'estBiaya')));
    }

    public function keluarStore(Request $request, $id)
    {
        $trx = TbTransaksi::with(['kendaraan', 'tarif', 'area'])
            ->where('id_parkir', $id)
            ->where('status', 'masuk')
            ->firstOrFail();

        $wk       = now();
        $dur      = max(1, (int) ceil(($wk->timestamp - $trx->waktu_masuk->timestamp) / 3600));
        $biaya    = $dur * $trx->tarif->tarif_per_jam;

        $trx->update([
            'waktu_keluar' => $wk,
            'durasi_jam'   => $dur,
            'biaya_total'  => $biaya,
            'status'       => 'keluar',
        ]);

        $trx->area->decrement('terisi');
        TbLogAktivitas::catat(Auth::id(), "Kendaraan keluar: {$trx->kendaraan->plat_nomor} — {$trx->area->nama_area} — Rp " . number_format($biaya, 0, ',', '.'));

        session(['last_trx' => $id]);

        return redirect()->route('petugas.struk.show', $id)
            ->with('success', "Kendaraan {$trx->kendaraan->plat_nomor} selesai. Biaya: Rp " . number_format($biaya, 0, ',', '.'));
    }
}
