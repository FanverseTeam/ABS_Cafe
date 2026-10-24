<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->input('role') !== 'all') {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:pengguna,username',
            'email' => 'required|email|max:255|unique:pengguna,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,kasir,staf',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'username.unique' => 'Username ini sudah dipakai oleh pengguna lain.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
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

        return back()->with('success', "Pengguna {$user->name} berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:pengguna,username,'.$user->id,
            'email' => 'required|email|max:255|unique:pengguna,email,'.$user->id,
            'role' => 'required|in:admin,kasir,staf',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $user->update([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
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

        return back()->with('success', "Data pengguna {$user->name} berhasil disimpan.");
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
}
