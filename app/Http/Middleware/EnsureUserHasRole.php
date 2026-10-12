<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Batasi akses route hanya untuk peran tertentu, mis. `role:owner`.
     * Guest diarahkan ke halaman login peran terkait; peran yang tidak cocok
     * mendapat halaman 403 yang ramah.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            $loginRoute = in_array('owner', $roles, true) ? 'owner.login' : 'admin.login';

            return redirect()->route($loginRoute)
                ->with('error', 'Silakan masuk terlebih dahulu untuk melanjutkan.');
        }

        $user = Auth::user();

        if ($user->status !== 'aktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Akun Anda sedang dinonaktifkan. Hubungi administrator.');
        }

        if (! in_array($user->primaryRole(), $roles, true)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
