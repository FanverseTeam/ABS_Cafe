<?php

namespace App\Services;

use App\Models\CafeSetting;
use App\Models\CashShift;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTimeline;
use App\Models\Promo;
use App\Models\Table;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Nomor pesanan unik berurutan: #ORD-00001, #ORD-00002, dst.
     */
    public function generateCode(): string
    {
        $last = Order::where('order_code', 'like', '#ORD-%')
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('order_code');

        $seq = $last ? ((int) substr($last, -5)) + 1 : 1;

        do {
            $code = '#ORD-'.str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
            $seq++;
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }

    /**
     * Hitung subtotal dari payload item (harga diambil ulang dari DB).
     *
     * @param  array<int, array{menu_id: int, variant_id?: int|null, quantity: int, notes?: string|null}>  $lines
     * @return array{items: array<int, array<string, mixed>>, subtotal: float}
     */
    public function buildLines(array $lines): array
    {
        $menus = Menu::whereIn('id', collect($lines)->pluck('menu_id')->unique())->get()->keyBy('id');
        $variantsByMenu = MenuVariant::whereIn('menu_id', $menus->keys())
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('menu_id');

        $built = [];
        $subtotal = 0.0;

        foreach ($lines as $line) {
            $menu = $menus->get((int) $line['menu_id']);
            if (! $menu) {
                throw ValidationException::withMessages(['cart' => 'Menu tidak ditemukan.']);
            }
            if (! $menu->is_available) {
                throw ValidationException::withMessages(['cart' => "Menu \"{$menu->name}\" sedang habis."]);
            }

            $menuVariants = $variantsByMenu->get($menu->id, collect());
            $quantity = max(1, (int) ($line['quantity'] ?? 1));
            $price = (float) $menu->price;
            $name = $menu->name;
            $variantId = null;
            $variant = null;

            if (! empty($line['variant_id'])) {
                $variant = $menuVariants->firstWhere('id', (int) $line['variant_id']);

                if (! $variant) {
                    throw ValidationException::withMessages(['cart' => "Varian untuk menu \"{$menu->name}\" tidak valid."]);
                }
            } elseif ($menuVariants->count() === 1) {
                $variant = $menuVariants->first();
            } elseif ($menuVariants->count() > 1) {
                throw ValidationException::withMessages(['cart' => "Pilih varian untuk menu \"{$menu->name}\"."]);
            }

            if ($variant) {
                if (! $variant->is_available) {
                    throw ValidationException::withMessages([
                        'cart' => "Varian \"{$variant->displayName()}\" untuk menu \"{$menu->name}\" sedang habis.",
                    ]);
                }

                $price = (float) $variant->price;
                $variantId = $variant->id;
                $name = $this->lineName($menu, $variant, $menuVariants->count());
            }

            $lineSubtotal = round($price * $quantity, 2);
            $subtotal += $lineSubtotal;

            $built[] = [
                'menu_id' => $menu->id,
                'variant_id' => $variantId,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'subtotal' => $lineSubtotal,
                'notes' => $line['notes'] ?? null,
            ];
        }

        if (empty($built)) {
            throw ValidationException::withMessages(['cart' => 'Keranjang masih kosong.']);
        }

        return ['items' => $built, 'subtotal' => round($subtotal, 2)];
    }

    private function lineName(Menu $menu, MenuVariant $variant, int $variantCount): string
    {
        if ($variantCount > 1 || str_contains($menu->name, ' / ')) {
            return trim($menu->variantBaseName().' - '.$variant->name.($variant->size ? ' - '.$variant->size : ''));
        }

        if ($variant->name === 'Porsi' && ! $variant->size) {
            return $menu->name;
        }

        if (str_contains($menu->name, $variant->name)) {
            return $variant->size ? $menu->name.' - '.$variant->size : $menu->name;
        }

        $display = $variant->displayName();

        return $menu->name.' ('.$display.')';
    }

    public function findPromo(?string $code): ?Promo
    {
        if (! $code) {
            return null;
        }

        $promo = Promo::whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])->first();

        if (! $promo) {
            throw ValidationException::withMessages(['voucher' => 'Kode voucher tidak ditemukan.']);
        }
        if (! $promo->isRunning()) {
            throw ValidationException::withMessages(['voucher' => 'Voucher belum aktif atau sudah berakhir.']);
        }
        if ($promo->remainingQuota() !== null && $promo->remainingQuota() <= 0) {
            throw ValidationException::withMessages(['voucher' => 'Kuota voucher sudah habis.']);
        }

        return $promo;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array{promo: Promo|null, discount: float}
     */
    public function calculateDiscount(?Promo $promo, array $items, float $subtotal): array
    {
        if (! $promo) {
            return ['promo' => null, 'discount' => 0.0];
        }

        $eligible = $this->eligibleSubtotal($promo, $items);

        if ($subtotal < (float) $promo->min_spend) {
            throw ValidationException::withMessages([
                'voucher' => 'Minimal belanja Rp '.number_format((float) $promo->min_spend, 0, ',', '.').' untuk memakai voucher ini.',
            ]);
        }

        $discount = match ($promo->type) {
            'persen' => round($eligible * (float) $promo->value / 100, 2),
            'nominal' => min((float) $promo->value, $eligible),
            'buy_x_get_y' => $this->buyXGetYDiscount($promo, $items),
            default => 0.0,
        };

        return ['promo' => $promo, 'discount' => round(min($discount, $subtotal), 2)];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function eligibleSubtotal(Promo $promo, array $items): float
    {
        if ($promo->applies_to === 'kategori' && $promo->target_category_id) {
            $menuIds = Menu::where('category_id', $promo->target_category_id)->pluck('id');

            return (float) collect($items)->whereIn('menu_id', $menuIds)->sum('subtotal');
        }

        if ($promo->applies_to === 'menu' && $promo->target_menu_id) {
            return (float) collect($items)->where('menu_id', $promo->target_menu_id)->sum('subtotal');
        }

        return (float) collect($items)->sum('subtotal');
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function buyXGetYDiscount(Promo $promo, array $items): float
    {
        $eligible = match (true) {
            $promo->applies_to === 'kategori' && (bool) $promo->target_category_id => collect($items)
                ->filter(fn ($i) => Menu::whereKey($i['menu_id'])->where('category_id', $promo->target_category_id)->exists()),
            $promo->applies_to === 'menu' && (bool) $promo->target_menu_id => collect($items)
                ->where('menu_id', $promo->target_menu_id),
            default => collect($items),
        };

        $units = [];
        foreach ($eligible as $item) {
            for ($i = 0; $i < (int) $item['quantity']; $i++) {
                $units[] = (float) $item['price'];
            }
        }

        $freeCount = intdiv(count($units), 3);
        if ($freeCount < 1) {
            return 0.0;
        }

        sort($units);

        return (float) array_sum(array_slice($units, 0, $freeCount));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createOrder(array $data, User $cashier, ?CashShift $shift): Order
    {
        return DB::transaction(function () use ($data, $cashier, $shift) {
            $built = $this->buildLines($data['items']);
            $promo = $this->findPromo($data['voucher'] ?? null);
            ['discount' => $discount] = $this->calculateDiscount($promo, $built['items'], $built['subtotal']);

            $taxPercent = (float) CafeSetting::get('tax_percent', 10);
            $serviceActive = (bool) CafeSetting::get('service_charge_active', 0);
            $servicePercent = $serviceActive ? (float) CafeSetting::get('service_charge_percent', 5) : 0;

            $tax = round(($built['subtotal'] - $discount) * $taxPercent / 100, 2);
            $service = round(($built['subtotal'] - $discount) * $servicePercent / 100, 2);
            $total = round($built['subtotal'] + $tax + $service - $discount, 2);

            $order = Order::create([
                'order_code' => $this->generateCode(),
                'customer_name' => $data['customer_name'] ?: 'Pelanggan',
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'table_number' => $data['order_type'] === 'dine_in' ? ($data['table_number'] ?? null) : null,
                'order_type' => $data['order_type'] ?? 'dine_in',
                'source' => $data['source'] ?? 'offline',
                'subtotal' => $built['subtotal'],
                'tax' => $tax,
                'service_charge' => $service,
                'discount' => $discount,
                'promo_id' => $promo?->id,
                'total' => $total,
                'payment_method' => $data['payment_method'] ?? 'tunai',
                'payment_status' => 'menunggu_pembayaran',
                'order_status' => $data['order_status'] ?? 'diproses',
                'cashier_name' => $cashier->name,
                'cashier_id' => $cashier->id,
                'cash_shift_id' => $shift?->id,
            ]);

            foreach ($built['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'notes' => $item['notes'],
                ]);
            }

            $this->addTimeline($order, 'DIBUAT', $cashier->name,
                'Pesanan dibuat oleh kasir ('.($order->source === 'web' ? 'Web' : 'Offline').')');

            return $order->fresh(['items', 'timelines']);
        });
    }

    public function addTimeline(Order $order, string $status, ?string $actor, string $description): OrderTimeline
    {
        return OrderTimeline::create([
            'order_id' => $order->id,
            'status' => strtoupper($status),
            'actor' => $actor,
            'description' => $description,
            'logged_at' => now(),
        ]);
    }

    /**
     * Alur status offline: diproses → siap → selesai.
     * Alur web: baru → diterima → diproses → siap → selesai.
     */
    public function statusFlow(Order $order): array
    {
        return $order->source === 'web'
            ? ['baru', 'diterima', 'diproses', 'siap', 'selesai']
            : ['baru', 'diproses', 'siap', 'selesai'];
    }

    public function nextStatus(Order $order): ?string
    {
        $flow = $this->statusFlow($order);
        $idx = array_search($order->order_status, $flow, true);

        return $idx !== false && isset($flow[$idx + 1]) ? $flow[$idx + 1] : null;
    }

    public function advanceStatus(Order $order, User $actor, ?string $target = null): Order
    {
        $target = $target ?: $this->nextStatus($order);
        $flow = $this->statusFlow($order);
        $currIdx = array_search($order->order_status, $flow, true);
        $targetIdx = $target ? array_search($target, $flow, true) : false;

        if (! $target || $targetIdx === false || ($currIdx !== false && $targetIdx <= $currIdx)) {
            throw ValidationException::withMessages(['status' => 'Status tujuan tidak valid untuk pesanan ini.']);
        }

        $description = match ($target) {
            'diterima' => 'Pesanan Web diterima kasir',
            'diproses' => 'Pesanan mulai dimasak bar/dapur',
            'siap' => 'Pesanan siap disajikan',
            'selesai' => $order->order_type === 'take_away' ? 'Pesanan diambil pelanggan' : 'Pesanan selesai disajikan',
            default => 'Status pesanan diperbarui',
        };

        $order->update(['order_status' => $target]);
        $this->addTimeline($order, $target, $actor->name, $description);

        if ($target === 'selesai') {
            $this->releaseTable($order);
        }

        return $order->fresh();
    }

    public function cancel(Order $order, User $actor, string $reason): Order
    {
        return DB::transaction(function () use ($order, $actor, $reason) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();
            if ($lockedOrder->isCancelled()) {
                return $lockedOrder;
            }

            $lockedOrder->update([
                'payment_status' => 'dibatalkan',
                'order_status' => 'dibatalkan',
                'cancel_reason' => $reason,
            ]);

            $this->addTimeline($lockedOrder, 'DIBATALKAN', $actor->name, "Alasan: {$reason}");
            $this->releaseTable($lockedOrder);

            return $lockedOrder->fresh();
        });
    }

    public function refund(Order $order, User $actor, string $reason): Order
    {
        return $this->cancel($order, $actor, $reason);
    }

    public function releaseTable(Order $order): void
    {
        if (! $order->table_number) {
            return;
        }

        $stillOccupied = Order::where('table_number', $order->table_number)
            ->where('id', '!=', $order->id)
            ->active()
            ->exists();

        if (! $stillOccupied) {
            Table::where('number', $order->table_number)->update(['status' => 'tersedia']);
        }
    }
}
