<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TbKendaraan;
use App\Models\User;
use App\Models\TbAreaParkir;
use App\Models\TbTarif;
use App\Models\TbLogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KendaraanController extends Controller
{
    private function stats(): array
    {
        return [
            'total_user'  => User::count(),
            'area_aktif'  => TbAreaParkir::where('status', 1)->count(),
            'total_kendaraan' => TbKendaraan::count(),
            'log_hari'    => TbLogAktivitas::whereDate('waktu_aktivitas', today())->count(),
        ];
    }

    public function index(Request $request)
    {
        $q     = $request->input('q', '');
        $jenis = $request->input('jenis', '');
        $jenisList = TbTarif::select('jenis_kendaraan')
                ->distinct()
                ->pluck('jenis_kendaraan')
                ->map(fn($j) => trim(strtolower($j)));
        $sort  = in_array($request->input('sort'), ['plat_nomor', 'jenis_kendaraan', 'pemilik']) ? $request->input('sort') : 'id_kendaraan';
        $order = $request->input('order', 'asc') === 'desc' ? 'desc' : 'asc';

        $query = TbKendaraan::query();
        if ($q) {
            $query->where(function ($q2) use ($q) {
                $q2->where('plat_nomor', 'like', "%{$q}%")
                   ->orWhere('pemilik',   'like', "%{$q}%")
                   ->orWhere('merek',     'like', "%{$q}%");
            });
        }
        if ($jenis) $query->where('jenis_kendaraan', $jenis);

        $kendaraans = $query->orderBy($sort, $order)->paginate(11)->withQueryString();
        return view('admin.kendaraan', array_merge($this->stats(), compact('kendaraans', 'q', 'jenis', 'sort', 'order', 'jenisList')));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plat_nomor'      => 'nullable|string|max:15|unique:tb_kendaraan,plat_nomor',
            'jenis_kendaraan' => 'required|string',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $jenis = $request->jenis_kendaraan;
            $plat = trim((string) $request->plat_nomor);
            if (strtolower($jenis) === 'sepeda') {
                if ($plat === '') {
                    do {
                        $code = 'SPD-' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
                    } while (TbKendaraan::where('plat_nomor', $code)->exists());
                    $plat = $code;
                }
            } else {
                $plat = strtoupper($plat);
            }

            // if still blank (shouldn't happen), set to null
            if ($plat === '') $plat = null;

            $kendaraan = TbKendaraan::create([
                'plat_nomor'      => $plat,
                'jenis_kendaraan' => $jenis,
                'merek'           => $request->merek ?? '',
                'warna'           => $request->warna ?? '',
                'pemilik'         => $request->pemilik ?? '',
                'foto'            => '',
                'created_at'      => now(),
            ]);

            $fotoName = '';
            if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
                $fotoName = time() . '_' . preg_replace('/[^a-z0-9]/', '_', strtolower($request->plat_nomor)) . '.' . $request->file('foto')->extension();
                $request->file('foto')->move(public_path('uploads/kendaraan'), $fotoName);
                $kendaraan->foto = $fotoName;
                $kendaraan->save();
            }

            TbLogAktivitas::catat(Auth::id(), "Menambah kendaraan: " . ($kendaraan->plat_nomor ?: '—') . " ({$request->jenis_kendaraan})");
            DB::commit();
            return back()->with('success', 'Kendaraan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            if (!empty($fotoName) && file_exists(public_path('uploads/kendaraan/' . $fotoName))) {
                @unlink(public_path('uploads/kendaraan/' . $fotoName));
            }
            report($e);
            return back()->with('error', 'Gagal menambahkan kendaraan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $kendaraan = TbKendaraan::findOrFail($id);
        $request->validate([
            'plat_nomor'      => 'nullable|string|max:15',
            'jenis_kendaraan' => 'required|string',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $fotoName = $kendaraan->foto;

        if ($request->input('hapus_foto') == '1') {
            if ($fotoName && file_exists(public_path('uploads/kendaraan/' . $fotoName))) unlink(public_path('uploads/kendaraan/' . $fotoName));
            $fotoName = '';
        } elseif ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            if ($fotoName && file_exists(public_path('uploads/kendaraan/' . $fotoName))) unlink(public_path('uploads/kendaraan/' . $fotoName));
            $fotoName = time() . '_' . preg_replace('/[^a-z0-9]/', '_', strtolower($request->plat_nomor)) . '.' . $request->file('foto')->extension();
            $request->file('foto')->move(public_path('uploads/kendaraan'), $fotoName);
        }

        // Handle plat logic: if jenis sepeda and plat not provided, generate code; otherwise validate uniqueness
        $jenis = $request->jenis_kendaraan;
        $plat = trim((string) $request->plat_nomor);
        if (strtolower($jenis) === 'sepeda') {
            if ($plat === '') {
                // generate a new unique SPD code
                do {
                    $code = 'SPD-' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
                } while (TbKendaraan::where('plat_nomor', $code)->exists());
                $plat = $code;
            }
        } else {
            $plat = strtoupper($plat);
        }

        // If plat provided, ensure uniqueness (exclude current)
        if ($plat) {
            $exists = TbKendaraan::where('plat_nomor', $plat)->where('id_kendaraan', '!=', $id)->exists();
            if ($exists) return back()->with('error', 'Plat nomor sudah dipakai.')->withInput();
        }

        $kendaraan->update([
            'plat_nomor'      => $plat ?: null,
            'jenis_kendaraan' => $jenis,
            'merek'           => $request->merek ?? '',
            'warna'           => $request->warna ?? '',
            'pemilik'         => $request->pemilik ?? '',
            'foto'            => $fotoName,
        ]);

        TbLogAktivitas::catat(Auth::id(), "Mengubah kendaraan id={$id}: " . strtoupper($request->plat_nomor));
        return back()->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kendaraan = TbKendaraan::findOrFail($id);
        if ($kendaraan->transaksis()->exists()) return back()->with('error', 'Kendaraan tidak bisa dihapus, ada riwayat transaksi.');
        if ($kendaraan->foto && file_exists(public_path('uploads/kendaraan/' . $kendaraan->foto))) unlink(public_path('uploads/kendaraan/' . $kendaraan->foto));
        $plat = $kendaraan->plat_nomor;
        $kendaraan->delete();
        TbLogAktivitas::catat(Auth::id(), "Menghapus kendaraan: $plat");
        return back()->with('success', 'Kendaraan berhasil dihapus.');
    }
}
