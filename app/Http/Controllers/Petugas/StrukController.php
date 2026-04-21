<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\TbTransaksi;
use App\Models\TbLogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StrukController extends Controller
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

    public function index(Request $request)
    {
        $q    = $request->input('q', '');
        $userArea = Auth::user()->id_area ?? null;

        $list = TbTransaksi::with('kendaraan')
            ->when($userArea, fn($query) => $query->where('id_area', $userArea))
            ->where('status', 'keluar')
            ->when($q, fn($query) => $query->whereHas('kendaraan', fn($k) => $k->where('plat_nomor', 'like', "%{$q}%")))
            ->orderByDesc('waktu_masuk')
            ->limit(10)
            ->get();

        $selectedId = session('last_trx');
        $trx = null;

        if ($selectedId) {
            $trx = TbTransaksi::with(['kendaraan', 'tarif', 'area', 'user'])
                ->where('id_parkir', $selectedId)
                ->where('status', 'keluar')
                ->first();
            if ($trx && $userArea && $trx->id_area != $userArea) {
                $trx = null;
            }
        }

        return view('petugas.struk', array_merge($this->stats(), compact('list', 'trx', 'q', 'selectedId')));
    }

    public function show($id)
    {
        $trx = TbTransaksi::with(['kendaraan', 'tarif', 'area', 'user'])
            ->where('id_parkir', $id)
            ->where('status', 'keluar')
            ->firstOrFail();

        $userArea = Auth::user()->id_area ?? null;
        if ($userArea && $trx->id_area != $userArea) {
            return back()->with('error', 'Anda tidak berwenang melihat transaksi di area ini.');
        }

        $list = TbTransaksi::with('kendaraan')
            ->where('status', 'keluar')
            ->orderByDesc('waktu_masuk')
            ->limit(10)
            ->get();

        $q = '';

        return view('petugas.struk', array_merge($this->stats(), compact('trx', 'list', 'q')));
    }

    public function print($id)
    {
        $trx = TbTransaksi::with(['kendaraan', 'tarif', 'area', 'user'])
            ->where('id_parkir', $id)
            ->where('status', 'keluar')
            ->firstOrFail();

        $userArea = Auth::user()->id_area ?? null;
        if ($userArea && $trx->id_area != $userArea) {
            return back()->with('error', 'Anda tidak berwenang mencetak struk di area ini.');
        }

        TbLogAktivitas::catat(Auth::id(), "Mencetak struk transaksi TRX-{$id}");

        return view('petugas.struk_print', compact('trx'));
    }
}
