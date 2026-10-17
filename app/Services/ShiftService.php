<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Models\CashAdjustment;
use App\Models\CashMovement;
use App\Models\CashShift;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShiftService
{
    /**
     * Dapatkan shift aktif di laci saat ini (satu laci kas bersama untuk semua kasir).
     */
    public function activeDrawerShift(): ?CashShift
    {
        return CashShift::with('user')
            ->where('status', 'buka')
            ->latest('opened_at')
            ->first();
    }

    /**
     * Shift aktif untuk user saat ini atau laci aktif.
     */
    public function activeFor(User $user): ?CashShift
    {
        return CashShift::where('status', 'buka')
            ->where('user_id', $user->id)
            ->latest('opened_at')
            ->first() ?? $this->activeDrawerShift();
    }

    /**
     * Hitung kas awal otomatis untuk shift berikutnya beserta sumber keterangannya.
     *
     * @return array{amount: float, source_text: string, source_key: string, last_shift: ?CashShift, adjustments_total: float}
     */
    public function calculateNextOpeningCash(): array
    {
        $lastClosed = CashShift::with('user')
            ->where('status', 'tutup')
            ->latest('closed_at')
            ->first();

        $unappliedAdjustments = CashAdjustment::whereNull('applied_to_shift_id')->get();
        $adjustmentsTotal = (float) $unappliedAdjustments->sum('amount');

        if ($lastClosed) {
            $baseCash = (float) $lastClosed->closing_cash;
            $cashierName = $lastClosed->user?->name ?? 'Kasir sebelumnya';
            $timeString = $lastClosed->closed_at
                ? $lastClosed->closed_at->translatedFormat('d M Y, H:i')
                : '-';
            $sourceText = "Dari shift sebelumnya oleh {$cashierName}, {$timeString}";
            $sourceKey = 'shift:'.$lastClosed->id;
        } else {
            $baseCash = (float) CafeSetting::get('default_opening_cash', 100000);
            $sourceText = 'Modal Awal Laci dari Pengaturan Bisnis';
            $sourceKey = 'setting:default_opening_cash';
        }

        if ($adjustmentsTotal != 0) {
            $adjFormatted = ($adjustmentsTotal > 0 ? '+' : '').'Rp '.number_format($adjustmentsTotal, 0, ',', '.');
            $sourceText .= " (penyesuaian kas: {$adjFormatted})";
        }

        $finalOpening = max(0, $baseCash + $adjustmentsTotal);

        return [
            'amount' => $finalOpening,
            'source_text' => $sourceText,
            'source_key' => $sourceKey,
            'last_shift' => $lastClosed,
            'adjustments_total' => $adjustmentsTotal,
        ];
    }

    /**
     * Buka shift kasir dengan kas awal otomatis (atau manual jika diberikan).
     */
    public function open(User $user, ?float $openingCash = null, ?string $notes = null): CashShift
    {
        return DB::transaction(function () use ($user, $openingCash, $notes) {
            $active = CashShift::where('status', 'buka')->lockForUpdate()->first();
            if ($active) {
                $activeName = $active->user?->name ?? 'kasir lain';
                throw ValidationException::withMessages([
                    'shift' => "Laci kas masih memiliki shift aktif oleh {$activeName}. Selesaikan atau tutup shift tersebut terlebih dahulu.",
                ]);
            }

            $calc = $this->calculateNextOpeningCash();
            $finalOpening = $openingCash !== null ? (float) $openingCash : (float) $calc['amount'];
            $sourceText = $calc['source_text'];

            $shift = CashShift::create([
                'user_id' => $user->id,
                'opened_at' => now(),
                'opening_cash' => $finalOpening,
                'opening_cash_source' => $sourceText,
                'expected_cash' => $finalOpening,
                'closing_cash' => 0,
                'cash_difference' => 0,
                'total_sales' => 0,
                'total_transactions' => 0,
                'status' => 'buka',
                'is_auto_closed' => false,
                'notes' => $notes,
            ]);

            // Tautkan penyesuaian kas yang belum terpakai ke shift baru ini
            CashAdjustment::whereNull('applied_to_shift_id')
                ->update(['applied_to_shift_id' => $shift->id]);

            return $shift;
        });
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
     * Rekap kas berjalan shift.
     * Kas tunai berjalan = kas awal + penjualan tunai + kas masuk - kas keluar (kembalian sudah terhitung).
     *
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

    /**
     * Tutup shift kasir.
     * Bila mode wajib hitung fisik aktif dan closingCash diberikan, hitung selisih.
     * Bila mode otomatis, closing_cash = expected_cash, cash_difference = 0.
     */
    public function close(CashShift $shift, ?float $closingCash = null, ?string $notes = null): CashShift
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
            $lockedShift = CashShift::where('id', $shift->id)->lockForUpdate()->firstOrFail();
            if ($lockedShift->status !== 'buka') {
                throw ValidationException::withMessages(['shift' => 'Shift ini sudah ditutup.']);
            }

            $summary = $this->summary($lockedShift);
            $requirePhysical = (bool) CafeSetting::get('require_physical_cash_count', '0');

            if ($closingCash !== null) {
                $finalClosing = round((float) $closingCash, 2);
                $difference = round($finalClosing - $summary['expected_cash'], 2);
                $isAuto = false;
            } else {
                $finalClosing = $summary['expected_cash'];
                $difference = 0.00;
                $isAuto = true;
            }

            $lockedShift->update([
                'closed_at' => now(),
                'expected_cash' => $summary['expected_cash'],
                'closing_cash' => $finalClosing,
                'cash_difference' => $difference,
                'total_sales' => $summary['sales'],
                'total_transactions' => $summary['orders_count'],
                'status' => 'tutup',
                'is_auto_closed' => $isAuto,
                'notes' => $notes ?: $lockedShift->notes,
            ]);

            return $lockedShift->fresh();
        });
    }

    /**
     * Buat penyesuaian kas laci oleh Owner atau Admin.
     */
    public function createAdjustment(User $user, float $amount, string $reason): CashAdjustment
    {
        if (! in_array($user->primaryRole(), ['admin', 'owner'], true)) {
            abort(403, 'Hanya Owner atau Admin yang dapat membuat penyesuaian kas.');
        }

        if (empty(trim($reason))) {
            throw ValidationException::withMessages(['reason' => 'Alasan penyesuaian kas wajib diisi.']);
        }

        return DB::transaction(function () use ($user, $amount, $reason) {
            $adjustment = CashAdjustment::create([
                'user_id' => $user->id,
                'amount' => round($amount, 2),
                'reason' => trim($reason),
                'applied_to_shift_id' => null,
            ]);

            $formattedAmount = ($amount >= 0 ? '+' : '').'Rp '.number_format($amount, 0, ',', '.');

            ActivityLog::create([
                'user_name' => $user->name,
                'role' => $user->primaryRole(),
                'activity' => "Penyesuaian kas laci {$formattedAmount} (Alasan: {$reason})",
                'activity_type' => 'sistem',
                'ip_address' => request()->ip(),
            ]);

            return $adjustment;
        });
    }
}
