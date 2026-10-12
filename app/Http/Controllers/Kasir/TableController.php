<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableController extends Controller
{
    public function update(Request $request, Table $table)
    {
        $data = $request->validate([
            'status' => 'required|in:tersedia,terpakai,nonaktif',
            'location_notes' => 'nullable|string|max:200',
        ]);

        if ($data['status'] === 'nonaktif' && $table->orders()->active()->exists()) {
            return back()->with('error', "Meja {$table->number} sedang terisi, tidak bisa dinonaktifkan.");
        }

        $table->update($data);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->primaryRole(),
            'activity' => "Mengubah status Meja {$table->number} menjadi {$table->statusBadge()['label']}",
            'activity_type' => 'meja',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Status Meja {$table->number} diperbarui.");
    }
}
