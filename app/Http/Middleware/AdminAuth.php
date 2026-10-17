<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses halaman admin.');
        }

        $user = Auth::user();

        if ($user->status !== 'aktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda sedang dinonaktifkan. Hubungi administrator.');
        }

        if ($user->primaryRole() !== 'admin') {
            Auth::logout();

            return redirect()->route('admin.login')->with('error', 'Akses ditolak. Hanya peran Admin yang dapat mengakses back-office ini.');
        }

        return $next($request);
    }
}
