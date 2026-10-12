<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\CashShift;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShiftService
{
    public function activeFor(User $user): ?CashShift
    {
        return CashShift::where('user_id', $user->id)
            ->where('status', 'buka')
            ->latest('opened_at')
            ->first();
    }

    public function open(User $user, float $openingCash, ?string $notes = null): CashShift
    {
        if ($this->activeFor($user)) {
            throw ValidationException::withMessages(['shift' => 'Anda masih memiliki shift yang belum ditutup.']);
        }

        return CashShift::create([
            'user_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => $openingCash,
            'expected_cash' => $openingCash,
            'closing_cash' => 0,
            'cash_difference' => 0,
            'total_sales' => 0,
            'total_transactions' => 0,
            'status' => 'buka',
            'notes' => $notes,
        ]);
    }

    /**
     * @return array<int, array{method: string, label: string, count: int, total: float}>
     */
    public function methodBreakdown(CashShift $shift): array
    {
        return Order::where('cash_shift_id', $shift->id)
            ->where('payment_status', 'lunas')
            ->get(['payment_method', 'total'])
            ->groupBy('payment_method')
            ->map(fn ($rows, $method) => [
                'method' => $method ?: 'lainnya',
                'label' => (new Order(['payment_method' => $method]))->paymentMethodLabel(),
                'count' => $rows->count(),
                'total' => (float) $rows->sum('total'),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(CashShift $shift): array
    {
        $orders = Order::where('cash_shift_id', $shift->id)->where('payment_status', 'lunas');
        $paid = (clone $orders)->get();

        $cashSales = (float) $paid->where('payment_method', 'tunai')->sum('total');
        $cashIn = (float) $shift->cashMovements()->where('type', 'masuk')->sum('amount');
        $cashOut = (float) $shift->cashMovements()->where('type', 'keluar')->sum('amount');
        $expected = (float) $shift->opening_cash + $cashSales + $cashIn - $cashOut;

        return [
            'orders_count' => $orders->count(),
            'sales' => (float) $paid->sum('total'),
            'discount' => (float) $paid->sum('discount'),
            'cash_sales' => $cashSales,
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'expected_cash' => round($expected, 2),
            'methods' => $this->methodBreakdown($shift),
            'unpaid_count' => Order::where('cash_shift_id', $shift->id)->unpaid()->count(),
            'active_count' => Order::where('cash_shift_id', $shift->id)->active()->count(),
        ];
    }

    public function recordMovement(CashShift $shift, User $user, string $type, float $amount, string $reason): CashMovement
    {
        return $shift->cashMovements()->create([
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'reason' => $reason,
        ]);
    }

    public function close(CashShift $shift, float $closingCash, ?string $notes = null): CashShift
    {
        if ($shift->status !== 'buka') {
            throw ValidationException::withMessages(['shift' => 'Shift ini sudah ditutup.']);
        }

        $pending = Order::where('cash_shift_id', $shift->id)->active()->count();
        if ($pending > 0) {
            throw ValidationException::withMessages([
                'shift' => "Masih ada {$pending} pesanan aktif/belum lunas. Selesaikan sebelum menutup shift.",
            ]);
        }

        return DB::transaction(function () use ($shift, $closingCash, $notes) {
            $summary = $this->summary($shift);

            $shift->update([
                'closed_at' => now(),
                'expected_cash' => $summary['expected_cash'],
                'closing_cash' => $closingCash,
                'cash_difference' => round($closingCash - $summary['expected_cash'], 2),
                'total_sales' => $summary['sales'],
                'total_transactions' => $summary['orders_count'],
                'status' => 'tutup',
                'notes' => $notes ?: $shift->notes,
            ]);

            return $shift->fresh();
        });
    }
}
