<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TbAreaParkir;
use App\Models\User;
use App\Models\TbTarif;
use App\Models\TbLogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
    private function stats(): array
    {
        return [
            'total_user'  => User::count(),
            'area_aktif'  => TbAreaParkir::count(),
            'jenis_tarif' => TbTarif::count(),
            'log_hari'    => TbLogAktivitas::whereDate('waktu_aktivitas', today())->count(),
        ];
    }

    public function index()
    {
        $areas = TbAreaParkir::orderBy('id_area')->get();
        return view('admin.area', array_merge($this->stats(), compact('areas')));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_area' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1',
        ]);

        TbAreaParkir::create([
            'nama_area' => $request->nama_area,
            'alamat'    => $request->alamat ?? '',
            'kapasitas' => $request->kapasitas,
            'terisi'    => 0,
            'status'    => $request->has('status') ? 1 : 0,
        ]);

        TbLogAktivitas::catat(Auth::id(), "Menambah area parkir: {$request->nama_area} (kapasitas: {$request->kapasitas})");

        return back()->with('success', 'Area parkir berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $area = TbAreaParkir::findOrFail($id);
        $request->validate([
            'nama_area' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1',
        ]);

        $area->update([
            'nama_area' => $request->nama_area,
            'alamat'    => $request->alamat ?? '',
            'kapasitas' => $request->kapasitas,
            'status'    => $request->has('status') ? 1 : 0,
        ]);

        TbLogAktivitas::catat(Auth::id(), "Mengubah area parkir id={$id}: {$request->nama_area}");

        return back()->with('success', 'Area parkir berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $area = TbAreaParkir::findOrFail($id);

        if ($area->transaksis()->exists()) {
            return back()->with('error', 'Area tidak bisa dihapus, ada transaksi terkait.');
        }

        $area->delete();
        TbLogAktivitas::catat(Auth::id(), "Menghapus area id={$id}");

        return back()->with('success', 'Area parkir berhasil dihapus.');
    }
}
