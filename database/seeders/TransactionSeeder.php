<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\CashShift;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTimeline;
use App\Models\Payment;
use App\Models\Promo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    private array $customers = [
        'Rian Pratama', 'Dinda Ayu', 'Fajar Nugraha', 'Nadia Salsabila', 'Bagas Aditya',
        'Clara Anindya', 'Dimas Wicaksono', 'Mega Putri', 'Hendra Setiawan', 'Anisa Maulida',
        'Yogi Saputra', 'Kurnia Sandy', 'Bayu Pamungkas', 'Tiara Anggraini', 'Rizky Firmansyah',
        'Salsabila Putri', 'Zaky Alfarizi', 'Nadira Ramadhani', 'Ilham Prasetyo', 'Novi Safitri',
        'Doni Kusuma', 'Rani Octaviana', 'Eko Purnomo', 'Vina Melati', 'Arif Budiman',
    ];

    public function run(): void
    {
        // Idempotent: data historis (#ABS...) hanya di-seed sekali.
        if (Order::where('order_code', 'like', '#ABS%')->exists()) {
            return;
        }

        mt_srand(20261012);

        $kasirs = User::where('role', 'kasir')->where('status', 'aktif')->get();
        $menus = Menu::all();
        $promos = Promo::where('is_active', true)->get();
        $methods = ['tunai', 'qris', 'debit'];

        if ($kasirs->isEmpty() || $menus->isEmpty()) {
            return;
        }

        // Backfill cashier_id untuk pesanan lama yang hanya punya cashier_name.
        foreach (Order::whereNull('cashier_id')->whereNotNull('cashier_name')->get() as $legacy) {
            $match = $kasirs->firstWhere('name', $legacy->cashier_name);
            if ($match) {
                $legacy->cashier_id = $match->id;
                $legacy->save();
            }
        }

        $days = 80;
        $start = Carbon::today()->subDays($days - 1);

        for ($d = 0; $d < $days; $d++) {
            $date = $start->copy()->addDays($d);
            $isWeekend = in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY], true);
            $isToday = $date->isToday();

            $orderCount = $isWeekend ? mt_rand(13, 24) : mt_rand(7, 15);
            if ($isToday) {
                $orderCount = mt_rand(4, 8);
            }

            // Dua shift per hari: pagi (09:00-15:00) & sore (15:00-21:30)
            $openingPagi = mt_rand(3, 5) * 100000;
            $openingSore = mt_rand(3, 5) * 100000;

            $pagi = $this->createShift($kasirs->random(), $date, '09:00', '15:00', $openingPagi);
            $sore = $this->createShift($kasirs->random(), $date, '15:00', '21:30', $openingSore);

            $shiftStats = [
                $pagi->id => ['cash' => 0.0, 'sales' => 0.0, 'count' => 0, 'opening' => (float) $openingPagi],
                $sore->id => ['cash' => 0.0, 'sales' => 0.0, 'count' => 0, 'opening' => (float) $openingSore],
            ];

            $sequence = 0;

            for ($i = 0; $i < $orderCount; $i++) {
                $hour = mt_rand(9, 21);
                $shift = $hour < 15 ? $pagi : $sore;
                $cashier = $shift->user;

                $method = $methods[array_rand($methods)];
                $orderType = mt_rand(1, 100) <= 72 ? 'dine_in' : 'take_away';
                $tableNumber = $orderType === 'dine_in' ? mt_rand(1, 10) : null;

                $createdAt = $date->copy()->setTime($hour, mt_rand(0, 59), mt_rand(0, 59));
                if ($isToday && $createdAt->greaterThan(now())) {
                    $createdAt = now()->subMinutes(mt_rand(5, 120));
                }
                if ($createdAt->greaterThan(now())) {
                    $createdAt = $date->copy()->setTime(12, 0, 0);
                }

                // Pilih 1-4 menu
                $itemCount = mt_rand(1, 4);
                $selected = $menus->random($itemCount);
                $subtotal = 0.0;
                $items = [];

                foreach ($selected as $menu) {
                    $qty = mt_rand(1, 3);
                    $lineSubtotal = (float) $menu->price * $qty;
                    $subtotal += $lineSubtotal;
                    $items[] = [
                        'menu_id' => $menu->id,
                        'name' => $menu->name,
                        'price' => (float) $menu->price,
                        'quantity' => $qty,
                        'subtotal' => $lineSubtotal,
                    ];
                }

                // Terapkan promo sesekali
                $discount = 0.0;
                $promoId = null;
                if ($promos->isNotEmpty() && mt_rand(1, 100) <= 18) {
                    $promo = $promos->random();
                    if ($subtotal >= (float) $promo->min_spend) {
                        $promoId = $promo->id;
                        $discount = match ($promo->type) {
                            'persen' => round($subtotal * (float) $promo->value / 100),
                            'nominal' => min((float) $promo->value, $subtotal),
                            default => (float) $selected->min('price'),
                        };
                    }
                }

                $tax = round($subtotal * 0.10);
                $total = max(0, $subtotal + $tax - $discount);

                // Sebagian kecil pesanan hari ini belum lunas
                $isPending = $isToday && mt_rand(1, 100) <= 20;
                $paymentStatus = $isPending ? 'menunggu_pembayaran' : 'lunas';
                $orderStatus = $isPending ? 'diproses' : 'selesai';
                $paidAt = $isPending ? null : $createdAt->copy()->addMinutes(mt_rand(2, 15));

                $sequence++;
                $order = new Order([
                    'order_code' => sprintf('#ABS%s%03d', $createdAt->format('ymd'), $sequence),
                    'customer_name' => $this->customers[array_rand($this->customers)],
                    'table_number' => $tableNumber,
                    'order_type' => $orderType,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'service_charge' => 0,
                    'discount' => $discount,
                    'promo_id' => $promoId,
                    'total' => $total,
                    'payment_method' => $method,
                    'payment_status' => $paymentStatus,
                    'order_status' => $orderStatus,
                    'cashier_name' => $cashier->name,
                    'cashier_id' => $cashier->id,
                    'cash_shift_id' => $shift->id,
                    'paid_at' => $paidAt,
                ]);
                $order->created_at = $createdAt;
                $order->updated_at = $createdAt;
                $order->save();

                foreach ($items as $item) {
                    $orderItem = new OrderItem($item + ['order_id' => $order->id, 'notes' => null]);
                    $orderItem->created_at = $createdAt;
                    $orderItem->updated_at = $createdAt;
                    $orderItem->save();
                }

                OrderTimeline::create([
                    'order_id' => $order->id,
                    'status' => 'DIBUAT',
                    'actor' => 'Sistem Meja',
                    'description' => "Pesanan dibuat dari {$orderType} ".($tableNumber ? "Meja {$tableNumber}" : ''),
                    'logged_at' => $createdAt,
                ]);

                if ($paymentStatus === 'lunas') {
                    OrderTimeline::create([
                        'order_id' => $order->id,
                        'status' => 'SIAP',
                        'actor' => 'Barista / Kitchen',
                        'description' => 'Pesanan siap disajikan',
                        'logged_at' => $createdAt->copy()->addMinutes(mt_rand(6, 14)),
                    ]);

                    OrderTimeline::create([
                        'order_id' => $order->id,
                        'status' => 'LUNAS',
                        'actor' => $cashier->name,
                        'description' => 'Pembayaran '.strtoupper($method).' lunas diverifikasi kasir',
                        'logged_at' => $paidAt,
                    ]);

                    $cashReceived = $method === 'tunai' ? ceil($total / 5000) * 5000 : $total;
                    $payment = new Payment([
                        'order_id' => $order->id,
                        'cashier_id' => $cashier->id,
                        'method' => $method,
                        'amount' => $total,
                        'cash_received' => $cashReceived,
                        'change_given' => max(0, $cashReceived - $total),
                        'reference' => $method === 'qris' ? 'QR'.mt_rand(100000, 999999) : null,
                        'status' => 'lunas',
                        'paid_at' => $paidAt,
                    ]);
                    $payment->created_at = $paidAt;
                    $payment->updated_at = $paidAt;
                    $payment->save();

                    $shiftStats[$shift->id]['sales'] += $total;
                    $shiftStats[$shift->id]['count']++;
                    if ($method === 'tunai') {
                        $shiftStats[$shift->id]['cash'] += $total;
                    }
                }
            }

            // Finalisasi rekap shift
            foreach ([$pagi, $sore] as $shift) {
                $stat = $shiftStats[$shift->id];
                $expected = $stat['opening'] + $stat['cash'];
                $variance = mt_rand(1, 100) <= 75 ? 0 : (mt_rand(0, 1) ? -mt_rand(500, 5000) : mt_rand(500, 5000));
                $closing = $expected + $variance;

                $shift->update([
                    'expected_cash' => $expected,
                    'closing_cash' => $closing,
                    'cash_difference' => $closing - $expected,
                    'total_sales' => $stat['sales'],
                    'total_transactions' => $stat['count'],
                    'status' => 'tutup',
                ]);
            }
        }

        ActivityLog::create([
            'user_name' => 'Sistem',
            'role' => 'admin',
            'activity' => 'Seed '.$days.' hari data transaksi historis ABS Cafe',
            'activity_type' => 'sistem',
            'ip_address' => '127.0.0.1',
        ]);
    }

    private function createShift(User $user, Carbon $date, string $openTime, string $closeTime, int $openingCash): CashShift
    {
        $opened = $date->copy()->setTime(...array_map('intval', explode(':', $openTime)));
        $closed = $date->copy()->setTime(...array_map('intval', explode(':', $closeTime)));

        if ($opened->greaterThan(now())) {
            $opened = now()->subMinutes(30);
            $closed = now();
        }
        if ($closed->greaterThan(now())) {
            $closed = now();
        }

        $shift = new CashShift([
            'user_id' => $user->id,
            'opened_at' => $opened,
            'closed_at' => $closed,
            'opening_cash' => $openingCash,
            'expected_cash' => $openingCash,
            'closing_cash' => $openingCash,
            'cash_difference' => 0,
            'total_sales' => 0,
            'total_transactions' => 0,
            'status' => $closed->lt(now()) ? 'tutup' : 'buka',
        ]);
        $shift->created_at = $opened;
        $shift->updated_at = $opened;
        $shift->save();
        $shift->setRelation('user', $user);

        return $shift;
    }
}
