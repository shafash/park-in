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
use Illuminate\Support\Facades\Cache;
use App\Services\StatsService;

class KendaraanController extends Controller
{
    private StatsService $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    private function stats(): array
    {
        return $this->statsService->adminStats();
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
                $platRaw = trim((string) $request->plat_nomor);
                $plat = ''; // Ensure $plat is defined
            if (strtolower($jenis) === 'sepeda') {
                if ($platRaw === '') {
                    do {
                        $code = 'SPD-' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
                    } while (TbKendaraan::where('plat_nomor', $code)->exists());
                    // normalize generated code as well
                        $plat = $code; // Assign the generated code directly
                } else {
                    $plat = preg_replace('/[^A-Z0-9]/', '', strtoupper($platRaw));
                }
            } else {
                $plat = preg_replace('/[^A-Z0-9]/', '', strtoupper($platRaw));
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
                    $fotoBase = $plat ?? preg_replace('/[^A-Z0-9]/', '', strtoupper($platRaw));
                    $fotoName = time() . '_' . preg_replace('/[^a-z0-9]/', '_', strtolower($fotoBase)) . '.' . $request->file('foto')->extension();
                $request->file('foto')->move(public_path('uploads/kendaraan'), $fotoName);
                $kendaraan->foto = $fotoName;
                $kendaraan->save();
                // set cache so accessor won't hit filesystem for a while
                Cache::put('kendaraan:foto_exists:' . $fotoName, true, now()->addHours(6));
            }

            TbLogAktivitas::catat(Auth::id(), "Menambah kendaraan: " . ($kendaraan->plat_nomor ?: '—') . " ({$request->jenis_kendaraan})");
            DB::commit();
            return back()->with('success', 'Kendaraan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            if (!empty($fotoName) && file_exists(public_path('uploads/kendaraan/' . $fotoName))) {
                @unlink(public_path('uploads/kendaraan/' . $fotoName));
                Cache::forget('kendaraan:foto_exists:' . $fotoName);
            }
            // Report the exception for monitoring and debugging
            report($e);
            // Return a safe, generic message to the user without exposing internal details
            return back()->with('error', 'Gagal menambahkan kendaraan. Silakan coba lagi atau hubungi administrator.');
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

        // Normalize plate input early so foto naming and uniqueness checks use same value
        $platRaw = trim((string) $request->plat_nomor);
        $jenis = $request->jenis_kendaraan;
        if (strtolower($jenis) === 'sepeda') {
            $plat = trim((string) $request->plat_nomor);
            if ($plat === '') {
                do {
                    $code = 'SPD-' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
                } while (TbKendaraan::where('plat_nomor', $code)->exists());
                $plat = $code;
            }
        } else {
            $plat = preg_replace('/[^A-Z0-9]/', '', strtoupper($platRaw));
        }

        if ($request->input('hapus_foto') == '1') {
            if ($fotoName && file_exists(public_path('uploads/kendaraan/' . $fotoName))) {
                unlink(public_path('uploads/kendaraan/' . $fotoName));
                Cache::forget('kendaraan:foto_exists:' . $fotoName);
            }
            $fotoName = '';
        } elseif ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            if ($fotoName && file_exists(public_path('uploads/kendaraan/' . $fotoName))) {
                unlink(public_path('uploads/kendaraan/' . $fotoName));
                Cache::forget('kendaraan:foto_exists:' . $fotoName);
            }
                $fotoBase = $plat ?: preg_replace('/[^A-Z0-9]/', '', strtoupper($platRaw));
                $fotoName = time() . '_' . preg_replace('/[^a-z0-9]/', '_', strtolower($fotoBase)) . '.' . $request->file('foto')->extension();
            $request->file('foto')->move(public_path('uploads/kendaraan'), $fotoName);
            // cache new foto existence
            Cache::put('kendaraan:foto_exists:' . $fotoName, true, now()->addHours(6));
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

        TbLogAktivitas::catat(Auth::id(), "Mengubah kendaraan id={$id}: " . ($plat ?: '—'));
        return back()->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kendaraan = TbKendaraan::findOrFail($id);
        if ($kendaraan->transaksis()->exists()) return back()->with('error', 'Kendaraan tidak bisa dihapus, ada riwayat transaksi.');
        if ($kendaraan->foto && file_exists(public_path('uploads/kendaraan/' . $kendaraan->foto))) {
            unlink(public_path('uploads/kendaraan/' . $kendaraan->foto));
            Cache::forget('kendaraan:foto_exists:' . $kendaraan->foto);
        }
        $plat = $kendaraan->plat_nomor;
        $kendaraan->delete();
        TbLogAktivitas::catat(Auth::id(), "Menghapus kendaraan: $plat");
        return back()->with('success', 'Kendaraan berhasil dihapus.');
    }
}
