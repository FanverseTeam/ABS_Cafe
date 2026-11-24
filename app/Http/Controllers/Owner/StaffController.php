<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PenjagaKasir;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        if (Schema::hasTable('penjaga_kasir')) {
            $penjagaQuery = PenjagaKasir::orderBy('name');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $penjagaQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('bagian') && $request->input('bagian') !== 'all') {
                $penjagaQuery->where('bagian', $request->input('bagian'));
            }

            if ($request->filled('status') && $request->input('status') !== 'all') {
                $penjagaQuery->where('status', $request->input('status'));
            }

            $penjagaList = $penjagaQuery->paginate(15)->withQueryString();
        } else {
            $penjagaList = new LengthAwarePaginator([], 0, 15, 1, ['path' => $request->url()]);
        }

        return view('owner.staff.index', compact('penjagaList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'bagian' => 'required|in:kitchen,barista,kasir',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'name.required' => 'Nama staf wajib diisi.',
            'bagian.required' => 'Bagian kerja staf wajib dipilih.',
            'bagian.in' => 'Pilihan bagian kerja harus Kitchen, Barista, atau Kasir.',
        ]);

        $penjaga = PenjagaKasir::create([
            'name' => trim((string) $request->input('name')),
            'bagian' => $request->input('bagian', 'kasir'),
            'phone' => $request->input('phone'),
            'status' => $request->input('status', 'aktif'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menambahkan data staf: {$penjaga->name} ({$penjaga->bagianLabel()})",
            'activity_type' => 'pengguna',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('owner.staff.index')->with('success', "Staf {$penjaga->name} ({$penjaga->bagianLabel()}) berhasil didaftarkan!");
    }

    public function update(Request $request, $id)
    {
        $penjaga = PenjagaKasir::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'bagian' => 'required|in:kitchen,barista,kasir',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'name.required' => 'Nama staf wajib diisi.',
            'bagian.required' => 'Bagian kerja staf wajib dipilih.',
            'bagian.in' => 'Pilihan bagian kerja harus Kitchen, Barista, atau Kasir.',
        ]);

        $penjaga->update([
            'name' => trim((string) $request->input('name')),
            'bagian' => $request->input('bagian', 'kasir'),
            'phone' => $request->input('phone'),
            'status' => $request->input('status'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui data staf: {$penjaga->name} ({$penjaga->bagianLabel()})",
            'activity_type' => 'pengguna',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('owner.staff.index')->with('success', "Data staf {$penjaga->name} berhasil diperbarui.");
    }

    public function toggleStatus($id)
    {
        $penjaga = PenjagaKasir::findOrFail($id);
        $penjaga->status = ($penjaga->status === 'aktif') ? 'nonaktif' : 'aktif';
        $penjaga->save();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status staf {$penjaga->name} menjadi {$penjaga->status}",
            'activity_type' => 'pengguna',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status staf {$penjaga->name} diubah menjadi {$penjaga->status}.");
    }

    public function destroy($id)
    {
        $penjaga = PenjagaKasir::findOrFail($id);
        $name = $penjaga->name;
        $penjaga->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus data staf: {$name}",
            'activity_type' => 'pengguna',
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('owner.staff.index')->with('success', "Data staf {$name} berhasil dihapus.");
    }
}
