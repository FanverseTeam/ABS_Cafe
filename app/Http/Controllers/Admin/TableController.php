<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Table;
use App\Models\ActivityLog;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('number', 'asc')->get();
        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|integer|unique:tables,number',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:tersedia,terpakai,nonaktif',
            'location_notes' => 'nullable|string',
        ], [
            'number.unique' => 'Nomor meja ini sudah ada.',
        ]);

        $table = Table::create([
            'number' => $request->input('number'),
            'capacity' => $request->input('capacity'),
            'status' => $request->input('status'),
            'location_notes' => $request->input('location_notes'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menambahkan meja nomor #{$table->number} (Kapasitas: {$table->capacity})",
            'activity_type' => 'meja',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Meja #{$table->number} berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'number' => 'required|integer|unique:tables,number,' . $table->id,
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:tersedia,terpakai,nonaktif',
            'location_notes' => 'nullable|string',
        ]);

        $table->update([
            'number' => $request->input('number'),
            'capacity' => $request->input('capacity'),
            'status' => $request->input('status'),
            'location_notes' => $request->input('location_notes'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui meja #{$table->number}",
            'activity_type' => 'meja',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Meja #{$table->number} berhasil diperbarui.");
    }

    public function updateStatus(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        $status = $request->input('status');

        $table->update(['status' => $status]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status meja #{$table->number} menjadi {$status}",
            'activity_type' => 'meja',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status meja #{$table->number} diubah menjadi {$status}.");
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $num = $table->number;
        $table->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus meja #{$num}",
            'activity_type' => 'meja',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Meja #{$num} berhasil dihapus.");
    }
}
