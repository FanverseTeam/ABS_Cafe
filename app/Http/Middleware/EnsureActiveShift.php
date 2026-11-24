<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveShift
{
    /**
     * Shift selalu berjalan di background — middleware ini tidak lagi memblokir kasir.
     * Jika belum ada shift aktif, ShiftService::activeDrawerShift() akan membuat satu otomatis.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
