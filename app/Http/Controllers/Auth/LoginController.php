<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /**
     * Portal dashboard per peran. Peran yang belum memiliki portal (mis. kasir)
     * belum bisa masuk melalui login terpadu ini.
     *
     * @var array<string, array{label: string}>
     */
    public const ROLES = [
        'admin' => ['label' => 'Administrator'],
        'owner' => ['label' => 'Owner'],
        'kasir' => ['label' => 'Kasir'],
    ];

    public function show(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->status !== 'aktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $recallerName = Auth::guard()->getRecallerName();
                Cookie::queue(Cookie::forget($recallerName));

                return view('auth.login')->with('error', 'Akun Anda sedang dinonaktifkan. Hubungi administrator.');
            }

            return redirect($this->dashboardFor($user));
        }

        return view('auth.login');
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

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput()
                ->with('error', 'Email/username atau kata sandi tidak cocok. Silakan periksa kembali.');
        }

        $user = Auth::user();

        if ($user->status !== 'aktif') {
            Auth::logout();

            return back()->withInput()
                ->with('error', 'Akun Anda sedang dinonaktifkan. Hubungi administrator.');
        }

        // Peran tanpa portal belum bisa masuk.
        if (! array_key_exists($user->primaryRole(), self::ROLES)) {
            Auth::logout();

            return back()->withInput()
                ->with('error', 'Modul untuk peran ini belum tersedia. Fitur ini akan segera hadir.');
        }

        $user->update(['last_login_at' => now()]);

        ActivityLog::create([
            'user_name' => $user->name,
            'role' => $user->primaryRole(),
            'activity' => ucfirst($user->primaryRole())." {$user->name} berhasil masuk ke dashboard",
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        $request->session()->regenerate();

        return redirect($this->dashboardFor($user))
            ->with('success', 'Selamat datang kembali di ABS Cafe!');
    }

    public function logout(Request $request)
    {
        $role = 'admin';

        if (Auth::check()) {
            $user = Auth::user();
            $role = $user->primaryRole();

            ActivityLog::create([
                'user_name' => $user->name,
                'role' => $role,
                'activity' => ucfirst($role).' '.$user->name.' keluar dari sistem',
                'activity_type' => 'sistem',
                'ip_address' => $request->ip(),
            ]);

            Auth::logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $recallerName = Auth::guard()->getRecallerName();
        Cookie::queue(Cookie::forget($recallerName));

        return redirect()->route('staf.landing')->with('success', 'Anda telah berhasil keluar.');
    }

    private function dashboardFor(User $user): string
    {
        return match ($user->primaryRole()) {
            'owner' => route('owner.dashboard'),
            'admin' => route('admin.dashboard'),
            'kasir' => route('kasir.dashboard'),
            default => route('login'),
        };
    }
}
