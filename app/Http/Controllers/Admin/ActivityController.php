<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('activity', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && $request->input('type') !== 'all') {
            $query->where('activity_type', $request->input('type'));
        }

        if ($request->filled('user') && $request->input('user') !== 'all') {
            $query->where('user_name', $request->input('user'));
        }

        $logs = $query->paginate(15)->withQueryString();
        $users = User::pluck('name')->unique();

        return view('admin.activity.index', compact('logs', 'users'));
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'Log_Aktivitas_ABS_Cafe_'.date('Ymd_His').'.csv';
        $logs = ActivityLog::orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Waktu', 'Pengguna', 'Peran', 'Aktivitas', 'Kategori', 'Alamat IP'];

        $callback = function () use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($logs as $l) {
                fputcsv($file, [
                    $l->id,
                    $l->created_at->format('d/m/Y H:i:s'),
                    $l->user_name,
                    strtoupper($l->role),
                    $l->activity,
                    strtoupper($l->activity_type),
                    $l->ip_address ?? '127.0.0.1',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
