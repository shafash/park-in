<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TbTarif;
use App\Models\User;
use App\Models\TbAreaParkir;
use App\Models\TbLogAktivitas;
use App\Models\TbKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TarifController extends Controller
{
    private function stats(): array
    {
        return [
            'total_user'  => User::count(),
            'area_aktif'  => TbAreaParkir::where('status', 1)->count(),
            'total_kendaraan' => TbKendaraan::count(),
            'jenis_tarif' => TbTarif::count(),
            'log_hari'    => TbLogAktivitas::whereDate('waktu_aktivitas', today())->count(),
        ];
    }

    public function index()
    {
        $tarifs = TbTarif::orderBy('id_tarif')->get();
        return view('admin.tarif', array_merge($this->stats(), compact('tarifs')));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_kendaraan' => 'required|string|max:50',
            'tarif_awal'      => 'required|numeric|min:0',
            'tarif_per_jam'   => 'required|numeric|min:100',
            'tarif_maks_per_hari' => 'required|numeric|min:0',
            'denda_per_jam'   => 'required|numeric|min:0',
        ]);

        if (TbTarif::where('jenis_kendaraan', $request->jenis_kendaraan)->exists()) {
            return back()->with('error', "Tarif untuk '{$request->jenis_kendaraan}' sudah ada.");
        }

        TbTarif::create($request->only('jenis_kendaraan', 'tarif_awal', 'tarif_per_jam', 'tarif_maks_per_hari', 'denda_per_jam'));
        TbLogAktivitas::catat(Auth::id(), "Menambahkan tarif: {$request->jenis_kendaraan} = Rp {$request->tarif_awal} (awal), Rp {$request->tarif_per_jam}/jam, maks Rp {$request->tarif_maks_per_hari}");

        return back()->with('success', 'Tarif berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $tarif = TbTarif::findOrFail($id);
        $request->validate([
            'jenis_kendaraan' => 'required|string|max:50',
            'tarif_awal'      => 'required|numeric|min:0',
            'tarif_per_jam'   => 'required|numeric|min:100',
            'tarif_maks_per_hari' => 'required|numeric|min:0',
            'denda_per_jam'   => 'required|numeric|min:0',
        ]);

        $tarif->update($request->only('jenis_kendaraan', 'tarif_awal', 'tarif_per_jam', 'tarif_maks_per_hari', 'denda_per_jam'));
        TbLogAktivitas::catat(Auth::id(), "Mengubah tarif id={$id}: {$request->jenis_kendaraan} = Rp {$request->tarif_per_jam}/jam");

        return back()->with('success', 'Tarif berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tarif = TbTarif::findOrFail($id);

        if ($tarif->transaksis()->exists()) {
            return back()->with('error', 'Tarif tidak bisa dihapus, sudah dipakai dalam transaksi.');
        }

        $tarif->delete();
        TbLogAktivitas::catat(Auth::id(), "Menghapus tarif id={$id}");

        return back()->with('success', 'Tarif berhasil dihapus.');
    }
}
