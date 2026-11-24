<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Models\Menu;
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

            $targetOrderStatus = in_array($lockedOrder->order_status, ['baru', 'diterima'], true)
                ? 'diproses'
                : $lockedOrder->order_status;

            $penjagaName = session('active_penjaga_kasir')
                ?: session('active_cashier_name')
                ?: CafeSetting::get('active_penjaga_kasir')
                ?: $lockedOrder->cashier_name
                ?: $cashier->name;

            if (empty($penjagaName)) {
                throw ValidationException::withMessages([
                    'penjaga' => 'Wajib memilih nama penjaga kasir yang bertugas sebelum memproses pembayaran.',
                ]);
            }

            $lockedOrder->update([
                'payment_status' => 'lunas',
                'payment_method' => $method,
                'order_status' => $targetOrderStatus,
                'paid_at' => now(),
                'cashier_id' => $lockedOrder->cashier_id ?: $cashier->id,
<<<<<<< HEAD
                'cashier_name' => $penjagaName,
=======
                'cashier_name' => $penjagaName ?: ($lockedOrder->cashier_name ?: $cashier->name),
>>>>>>> 6ebee46b65a42102454bfc65150cba0c5d1831b5
            ]);

            foreach ($lockedOrder->items as $item) {
                if ($item->menu_id) {
                    Menu::where('id', $item->menu_id)->increment('sold_count', (int) $item->quantity);
                }
            }

            $this->orders->addTimeline($lockedOrder, 'LUNAS', $penjagaName,
                'Pembayaran '.strtoupper($method).' lunas diverifikasi kasir');

            if ($targetOrderStatus === 'diproses' && $lockedOrder->order_status !== 'diproses') {
                $this->orders->addTimeline($lockedOrder, 'DIPROSES', $penjagaName,
                    'Pesanan mulai diproses oleh tim bar / dapur');
            }

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
