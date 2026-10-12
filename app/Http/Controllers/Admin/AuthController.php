<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $field => $login,
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->status !== 'aktif') {
                Auth::logout();
                return back()->withInput()->with('error', 'Akun Anda sedang dinonaktifkan. Hubungi admin sistem.');
            }

            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withInput()->with('error', 'Akses ditolak. Hanya peran Admin yang dapat mengakses back-office ini.');
            }

            $user->update(['last_login_at' => now()]);

            ActivityLog::create([
                'user_name' => $user->name,
                'role' => $user->role,
                'activity' => "Admin {$user->name} berhasil masuk ke dashboard",
                'activity_type' => 'sistem',
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali di Dashboard ABS Cafe!');
        }

        return back()->withInput()->with('error', 'Email/username atau kata sandi tidak cocok. Silakan periksa kembali.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_name' => Auth::user()->name,
                'role' => Auth::user()->role,
                'activity' => "Admin " . Auth::user()->name . " keluar dari sistem",
                'activity_type' => 'sistem',
                'ip_address' => $request->ip(),
            ]);
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil keluar.');
    }
}
