<?php

namespace App\Http\Middleware;

use App\Models\CashShift;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveShift
{
    /**
     * Transaksi (POS, pembayaran, ubah status) hanya boleh diproses
     * apabila kasir memiliki shift yang sedang aktif.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $shift = CashShift::where('status', 'buka')->exists();

        if (! $shift) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda belum membuka shift.'], 422);
            }

            return redirect()->route('kasir.shift.open')
                ->with('error', 'Buka shift terlebih dahulu sebelum memproses transaksi.');
        }

        return $next($request);
    }
}
