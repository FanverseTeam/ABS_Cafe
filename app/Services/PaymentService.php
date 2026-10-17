<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(private readonly OrderService $orders) {}

    /**
     * @param  array{method: string, cash_received?: float, reference?: string|null}  $data
     */
    public function record(Order $order, User $cashier, array $data): Payment
    {
        if ($order->isCancelled()) {
            throw ValidationException::withMessages(['payment' => 'Pesanan sudah dibatalkan.']);
        }
        if ($order->isPaid()) {
            throw ValidationException::withMessages(['payment' => 'Pesanan ini sudah dibayar.']);
        }

        $method = $data['method'] ?? 'tunai';
        $total = (float) $order->total;
        $cashReceived = $method === 'tunai' ? (float) ($data['cash_received'] ?? 0) : $total;

        if ($method === 'tunai' && $cashReceived < $total) {
            throw ValidationException::withMessages(['cash_received' => 'Uang diterima kurang dari total tagihan.']);
        }

        return DB::transaction(function () use ($order, $cashier, $method, $total, $cashReceived, $data) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();
            if ($lockedOrder->isCancelled()) {
                throw ValidationException::withMessages(['payment' => 'Pesanan sudah dibatalkan.']);
            }
            if ($lockedOrder->isPaid()) {
                throw ValidationException::withMessages(['payment' => 'Pesanan ini sudah dibayar.']);
            }

            $payment = Payment::create([
                'order_id' => $lockedOrder->id,
                'cashier_id' => $cashier->id,
                'method' => $method,
                'amount' => $total,
                'cash_received' => $method === 'tunai' ? $cashReceived : $total,
                'change_given' => $method === 'tunai' ? round($cashReceived - $total, 2) : 0,
                'reference' => $data['reference'] ?? null,
                'status' => 'lunas',
                'paid_at' => now(),
            ]);

            $lockedOrder->update([
                'payment_status' => 'lunas',
                'payment_method' => $method,
                'paid_at' => now(),
                'cashier_id' => $lockedOrder->cashier_id ?: $cashier->id,
                'cashier_name' => $lockedOrder->cashier_name ?: $cashier->name,
            ]);

            $this->orders->decrementStock($lockedOrder->fresh('items'), $cashier);
            $this->orders->addTimeline($lockedOrder, 'LUNAS', $cashier->name,
                'Pembayaran '.strtoupper($method).' lunas diverifikasi kasir');
            $this->orders->releaseTable($lockedOrder);

            ActivityLog::create([
                'user_name' => $cashier->name,
                'role' => $cashier->primaryRole(),
                'activity' => "Menerima pembayaran {$lockedOrder->order_code} sebesar Rp ".number_format($total, 0, ',', '.').' via '.strtoupper($method),
                'activity_type' => 'transaksi',
                'ip_address' => request()->ip(),
            ]);

            return $payment->fresh();
        });
    }
}
