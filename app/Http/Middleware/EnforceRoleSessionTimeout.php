<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Models\CafeSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnforceRoleSessionTimeout
{
    /**
     * Menangani pemeriksaan batas waktu sesi (idle timeout) berdasarkan peran pengguna.
     * Kasir tidak memiliki batas waktu time out agar operasional transaksi tidak terganggu.
     * Admin dan Owner memiliki batas waktu time out demi keamanan data.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $role = $user->primaryRole();

        // Kasir: tidak ada batas waktu time out
        if ($role === 'kasir') {
            $request->session()->put('last_activity_time', now()->timestamp);

            return $next($request);
        }

        // Admin dan Owner: berikan batas waktu time out untuk keamanan
        if (in_array($role, ['admin', 'owner'], true)) {
            $timeoutMinutes = (int) (CafeSetting::get('admin_timeout_minutes') ?: config('auth.admin_timeout', 15));
            $timeoutSeconds = $timeoutMinutes * 60;

            $lastActivity = $request->session()->get('last_activity_time');

            if ($lastActivity !== null && (now()->timestamp - $lastActivity) > $timeoutSeconds) {
                ActivityLog::create([
                    'user_name' => $user->name,
                    'role' => $role,
                    'activity' => ucfirst($role)." {$user->name} keluar otomatis karena sesi tidak aktif melebihi {$timeoutMinutes} menit (keamanan)",
                    'activity_type' => 'sistem',
                    'ip_address' => $request->ip(),
                ]);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $recallerName = Auth::guard()->getRecallerName();
                Cookie::queue(Cookie::forget($recallerName));

                if (! empty($user->getRememberToken())) {
                    $user->setRememberToken(Str::random(60));
                    $user->save();
                }

                $message = "Sesi Anda telah berakhir karena tidak ada aktivitas selama {$timeoutMinutes} menit demi keamanan. Silakan masuk kembali.";

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => $message,
                        'redirect' => route('login'),
                    ], 401);
                }

                return redirect()->route('login')->with('error', $message);
            }

            $request->session()->put('last_activity_time', now()->timestamp);
        }

        return $next($request);
    }
}
