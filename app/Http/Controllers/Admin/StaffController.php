<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PenjagaKasir;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        // Query Data Orang Yang Jaga Kasir (Penjaga Kasir)
        if (Schema::hasTable('penjaga_kasir')) {
            $penjagaQuery = PenjagaKasir::orderBy('name');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $penjagaQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status') && $request->input('status') !== 'all') {
                $penjagaQuery->where('status', $request->input('status'));
            }

            $penjagaList = $penjagaQuery->paginate(15)->withQueryString();
        } else {
            $penjagaList = new LengthAwarePaginator([], 0, 15, 1, ['path' => $request->url()]);
        }

        return view('admin.staff.index', compact('penjagaList'));
    }

    public function create()
    {
        $hasKasirUser = User::where('role', 'kasir')->exists();

        return view('admin.staff.create', compact('hasKasirUser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:30|min:3|regex:/^[a-z0-9._]+$/|unique:pengguna,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,kasir,staf',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'username.unique' => 'Username ini sudah dipakai oleh pengguna lain.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.regex' => 'Username hanya boleh mengandung huruf kecil, angka, titik, dan garis bawah.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        // Role kasir hanya boleh 1 akun login
        if ($request->input('role') === 'kasir' && User::where('role', 'kasir')->exists()) {
            throw ValidationException::withMessages([
                'role' => 'Role Kasir hanya menggunakan 1 akun login terpusat. Untuk menambah orang yang jaga kasir, daftarkan namanya di tab Penjaga Kasir.',
            ]);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => null,
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'phone' => $request->input('phone'),
            'status' => $request->input('status'),
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80',
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menambahkan staf/pengguna baru: {$user->name} ({$user->role})",
            'activity_type' => 'pengguna',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.staff.index', ['tab' => 'akun'])->with('success', "Pengguna {$user->name} berhasil ditambahkan!");
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $hasKasirUser = User::where('role', 'kasir')->where('id', '!=', $user->id)->exists();

        return view('admin.staff.edit', compact('user', 'hasKasirUser'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:30|min:3|regex:/^[a-z0-9._]+$/|unique:pengguna,username,'.$user->id,
            'role' => 'required|in:admin,kasir,staf',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'username.unique' => 'Username ini sudah dipakai oleh pengguna lain.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.regex' => 'Username hanya boleh mengandung huruf kecil, angka, titik, dan garis bawah.',
        ]);

        // Jika mengubah role ke kasir, pastikan belum ada kasir lain
        if ($request->input('role') === 'kasir' && User::where('role', 'kasir')->where('id', '!=', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'role' => 'Role Kasir hanya boleh memiliki 1 akun login terpusat.',
            ]);
        }

        $user->update([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'role' => $request->input('role'),
            'phone' => $request->input('phone'),
            'status' => $request->input('status'),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->input('password'))]);
        }

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui data pengguna: {$user->name}",
            'activity_type' => 'pengguna',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.staff.index', ['tab' => 'akun'])->with('success', "Data pengguna {$user->name} berhasil disimpan.");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Jangan nonaktifkan akun sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->status = ($user->status === 'aktif') ? 'nonaktif' : 'aktif';
        $user->save();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status pengguna {$user->name} menjadi {$user->status}",
            'activity_type' => 'pengguna',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status {$user->name} diubah menjadi {$user->status}.");
    }

    public function resetPassword(Request $request, $id)
    {
        $data = $request->validate([
            'new_password' => 'nullable|string|min:6',
        ], [
            'new_password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::findOrFail($id);
        $newPass = $data['new_password'] ?? 'password123';

        $user->update(['password' => Hash::make($newPass)]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mereset password pengguna: {$user->name}",
            'activity_type' => 'pengguna',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Password {$user->name} berhasil direset.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus akun pengguna: {$name}",
            'activity_type' => 'pengguna',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Pengguna {$name} telah berhasil dihapus.");
    }

    // ═══ MANAJEMEN PENJAGA KASIR (Orang yang jaga kasir / tanpa akun login) ═══

    public function storePenjaga(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'name.required' => 'Nama orang yang jaga kasir wajib diisi.',
        ]);

        $penjaga = PenjagaKasir::create([
            'name' => trim((string) $request->input('name')),
            'phone' => $request->input('phone'),
            'status' => $request->input('status', 'aktif'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menambahkan data penjaga kasir: {$penjaga->name}",
            'activity_type' => 'pengguna',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', "Penjaga kasir {$penjaga->name} berhasil didaftarkan!");
    }

    public function updatePenjaga(Request $request, $id)
    {
        $penjaga = PenjagaKasir::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'name.required' => 'Nama penjaga kasir wajib diisi.',
        ]);

        $penjaga->update([
            'name' => trim((string) $request->input('name')),
            'phone' => $request->input('phone'),
            'status' => $request->input('status'),
        ]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui data penjaga kasir: {$penjaga->name}",
            'activity_type' => 'pengguna',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', "Data penjaga kasir {$penjaga->name} berhasil diperbarui.");
    }

    public function toggleStatusPenjaga($id)
    {
        $penjaga = PenjagaKasir::findOrFail($id);
        $penjaga->status = ($penjaga->status === 'aktif') ? 'nonaktif' : 'aktif';
        $penjaga->save();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Mengubah status penjaga kasir {$penjaga->name} menjadi {$penjaga->status}",
            'activity_type' => 'pengguna',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Status penjaga kasir {$penjaga->name} diubah menjadi {$penjaga->status}.");
    }

    public function destroyPenjaga($id)
    {
        $penjaga = PenjagaKasir::findOrFail($id);
        $name = $penjaga->name;
        $penjaga->delete();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Menghapus data penjaga kasir: {$name}",
            'activity_type' => 'pengguna',
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', "Data penjaga kasir {$name} berhasil dihapus.");
    }
}
