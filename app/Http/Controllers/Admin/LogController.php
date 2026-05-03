<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TbLogAktivitas;
use App\Models\User;
use App\Models\TbAreaParkir;
use App\Models\TbTarif;
use App\Models\TbKendaraan;
use Illuminate\Http\Request;
use App\Services\StatsService;

class LogController extends Controller
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

        $filename = 'log_aktivitas_' . now()->format('Ymd') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($query) {
            $f = fopen('php://output', 'w');
            // header
            fputcsv($f, ['ID', 'Nama', 'Role', 'Aktivitas', 'Waktu']);

            // stream rows in chunks to avoid high memory usage
            $query->chunkById(1000, function ($logs) use ($f) {
                foreach ($logs as $log) {
                    fputcsv($f, [
                        $log->id_log,
                        $log->user->nama_lengkap ?? '-',
                        $log->user->role ?? '-',
                        $log->aktivitas,
                        $log->waktu_aktivitas->format('d/m/Y H:i:s'),
                    ]);
                }
                // flush output buffers so client starts receiving data
                if (function_exists('ob_flush')) { @ob_flush(); }
                if (function_exists('flush')) { @flush(); }
            });

            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
}
