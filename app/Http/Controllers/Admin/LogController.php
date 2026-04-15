<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TbLogAktivitas;
use App\Models\User;
use App\Models\TbAreaParkir;
use App\Models\TbTarif;
use Illuminate\Http\Request;

class LogController extends Controller
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

    public function index(Request $request)
    {
        $role = $request->input('role', '');

        $query = TbLogAktivitas::with('user')->orderByDesc('waktu_aktivitas');

        if ($role) {
            $query->whereHas('user', fn($q) => $q->where('role', $role));
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('admin.log', array_merge($this->stats(), compact('logs', 'role')));
    }

    public function export(Request $request)
    {
        $role  = $request->input('role', '');
        $query = TbLogAktivitas::with('user')->orderByDesc('waktu_aktivitas');

        if ($role) {
            $query->whereHas('user', fn($q) => $q->where('role', $role));
        }

        $logs = $query->get();

        $filename = 'log_aktivitas_' . now()->format('Ymd') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($logs) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['ID', 'Nama', 'Role', 'Aktivitas', 'Waktu']);
            foreach ($logs as $log) {
                fputcsv($f, [
                    $log->id_log,
                    $log->user->nama_lengkap ?? '-',
                    $log->user->role ?? '-',
                    $log->aktivitas,
                    $log->waktu_aktivitas->format('d/m/Y H:i:s'),
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
}
