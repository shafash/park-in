<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TbAreaParkir;
use App\Models\TbTarif;
use App\Models\TbLogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrasiController extends Controller
{
    private function stats(): array
    {
        return [
            'total_user' => User::count(),
            'area_aktif'  => TbAreaParkir::where('status', 1)->count(),
            'jenis_tarif'=> TbTarif::count(),
            'log_hari'   => TbLogAktivitas::whereDate('waktu_aktivitas', today())->count(),
        ];
    }

    public function index()
    {
        $users = User::with('area')->orderBy('role')->orderBy('nama_lengkap')->paginate(10);
        $areas = TbAreaParkir::orderBy('nama_area')->get();
        return view('admin.registrasi', array_merge($this->stats(), compact('users', 'areas')));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:50',
            'username'     => 'required|string|max:50|unique:tb_user,username',
            'password'     => 'required|string|min:6',
            'role'         => 'required|in:petugas,owner',
            'id_area'      => 'nullable|exists:tb_area_parkir,id_area',
        ]);

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'status_aktif' => $request->has('status_aktif') ? 1 : 0,
            'id_area'      => $request->id_area
        ]);

        TbLogAktivitas::catat(Auth::id(), "Mendaftarkan user baru: {$request->username}");

        return back()->with('success', "User berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:50',
            'role'         => 'required|in:petugas,owner',
            'id_area'      => 'nullable|exists:tb_area_parkir,id_area',
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'role'         => $request->role,
            'status_aktif' => $request->has('status_aktif') ? 1 : 0,
            'id_area'      => $request->id_area
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        TbLogAktivitas::catat(Auth::id(), "Update user {$user->username}");

        return back()->with('success', 'User berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id_user === Auth::id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        if ($user->role === 'admin') {
            return back()->with('error', 'Akun admin tidak bisa dihapus.');
        }

        $username = $user->username;
        $user->delete();
        TbLogAktivitas::catat(Auth::id(), "Menghapus user: $username");

        return back()->with('success', 'User berhasil dihapus.');
    }
}
