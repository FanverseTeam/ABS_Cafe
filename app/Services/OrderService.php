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
use App\Models\Recipe;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\StockMovement;
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
        $variants = MenuVariant::whereIn('id', collect($lines)->pluck('variant_id')->filter()->unique())->get()->keyBy('id');

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

            $quantity = max(1, (int) ($line['quantity'] ?? 1));
            $price = (float) $menu->price;
            $name = $menu->name;
            $variantId = null;

            if (! empty($line['variant_id'])) {
                $variant = $variants->get((int) $line['variant_id']);
                if ($variant && $variant->menu_id === $menu->id) {
                    $price = (float) $variant->price;
                    $name = $menu->name.' ('.$variant->name.')';
                    $variantId = $variant->id;
                }
            }

            if ($menu->stock !== null && $menu->stock < $quantity) {
                throw ValidationException::withMessages(['cart' => "Stok \"{$menu->name}\" tersisa {$menu->stock}."]);
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

        return (float) collect($items)->sum('subtotal');
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function buyXGetYDiscount(Promo $promo, array $items): float
    {
        $eligible = $promo->applies_to === 'kategori' && $promo->target_category_id
            ? collect($items)->filter(fn ($i) => Menu::whereKey($i['menu_id'])->where('category_id', $promo->target_category_id)->exists())
            : collect($items);

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
            : ['diproses', 'siap', 'selesai'];
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

        if (! $target || ! in_array($target, $this->statusFlow($order), true)) {
            throw ValidationException::withMessages(['status' => 'Status tujuan tidak valid untuk pesanan ini.']);
        }

        $description = match ($target) {
            'diterima' => 'Pesanan Web diterima kasir',
            'diproses' => 'Pesanan mulai diproses bar/dapur',
            'siap' => 'Pesanan siap disajikan',
            'selesai' => $order->order_type === 'take_away' ? 'Pesanan diambil pelanggan' : 'Pesanan selesai disajikan',
            default => 'Status pesanan diperbarui',
        };

        $order->update(['order_status' => $target]);
        $this->addTimeline($order, $target, $actor->name, $description);

        return $order->fresh();
    }

    public function cancel(Order $order, User $actor, string $reason): Order
    {
        return DB::transaction(function () use ($order, $actor, $reason) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();
            if ($lockedOrder->isCancelled()) {
                return $lockedOrder;
            }

            // Kembalikan stok jika pesanan pernah lunas atau stok telah terpotong
            if ($lockedOrder->isPaid() || $lockedOrder->stock_deducted) {
                $this->restoreStock($lockedOrder, $actor, $reason);
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

    /**
     * Kurangi stok otomatis saat pesanan lunas (berdasarkan resep bahan & stok menu).
     */
    public function decrementStock(Order $order, User $actor): void
    {
        $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();
        if (! $lockedOrder || $lockedOrder->stock_deducted) {
            return;
        }

        // 1. Kumpulkan seluruh kebutuhan bahan baku resep
        $neededStocks = [];

        foreach ($lockedOrder->items as $item) {
            if (! $item->menu_id) {
                continue;
            }

            // Cari resep spesifik untuk varian, jika tidak ada cari resep dasar (variant_id null)
            $recipes = Recipe::where('menu_id', $item->menu_id)
                ->when($item->variant_id, fn ($q) => $q->where('menu_variant_id', $item->variant_id))
                ->get();

            if ($recipes->isEmpty() && $item->variant_id) {
                $recipes = Recipe::where('menu_id', $item->menu_id)
                    ->whereNull('menu_variant_id')
                    ->get();
            }

            if ($recipes->isNotEmpty()) {
                foreach ($recipes as $recipe) {
                    $qty = (float) $recipe->amount * (int) $item->quantity;
                    $neededStocks[$recipe->stock_id] = ($neededStocks[$recipe->stock_id] ?? 0.0) + $qty;
                }
            }
        }

        // 2. Validasi stok bahan baku mencukupi (stok tidak boleh minus)
        foreach ($neededStocks as $stockId => $needed) {
            $stock = Stock::where('id', $stockId)->lockForUpdate()->first();
            if (! $stock) {
                continue;
            }

            if ((float) $stock->quantity < $needed) {
                if ((float) $stock->quantity <= 0) {
                    $this->markMenusUnavailableForStock($stock);
                }
                throw ValidationException::withMessages([
                    'stock' => "Stok bahan \"{$stock->name}\" tidak mencukupi untuk pesanan {$lockedOrder->order_code} (dibutuhkan {$needed} {$stock->unit}, tersisa {$stock->quantity} {$stock->unit}).",
                ]);
            }
        }

        // Validasi stok langsung pada tabel menus jika menu memiliki batasan stok
        foreach ($lockedOrder->items as $item) {
            if (! $item->menu_id) {
                continue;
            }
            $menu = Menu::where('id', $item->menu_id)->lockForUpdate()->first();
            if ($menu && $menu->stock !== null && $menu->stock < (int) $item->quantity) {
                $menu->update(['is_available' => false]);
                throw ValidationException::withMessages([
                    'stock' => "Stok menu \"{$menu->name}\" tidak mencukupi (tersisa {$menu->stock} {$menu->unit}).",
                ]);
            }
        }

        // 3. Eksekusi pengurangan bahan baku & catat StockMovement
        foreach ($neededStocks as $stockId => $needed) {
            $stock = Stock::where('id', $stockId)->lockForUpdate()->first();
            if (! $stock) {
                continue;
            }

            $newQty = max(0.0, (float) $stock->quantity - $needed);
            $stock->update(['quantity' => $newQty]);

            StockMovement::create([
                'stock_id' => $stock->id,
                'user_id' => $actor->id,
                'type' => 'keluar',
                'quantity' => $needed,
                'stock_after' => $newQty,
                'note' => "Pengurangan otomatis pesanan {$lockedOrder->order_code}",
            ]);

            // Jika bahan habis (0), otomatis tandai menu yang membutuhkan bahan ini sebagai 'Habis'
            if ($newQty <= 0) {
                $this->markMenusUnavailableForStock($stock);
            }
        }

        // 4. Kurangi stok menu jika ada (kompatibilitas modul katalog lama)
        foreach ($lockedOrder->items as $item) {
            if (! $item->menu_id) {
                continue;
            }

            $menu = Menu::where('id', $item->menu_id)->lockForUpdate()->first();
            if (! $menu) {
                continue;
            }

            if ($menu->stock !== null) {
                $before = (int) $menu->stock;
                $after = max(0, $before - (int) $item->quantity);

                $menu->update([
                    'stock' => $after,
                    'sold_count' => (int) $menu->sold_count + (int) $item->quantity,
                    'is_available' => $after > 0,
                ]);

                StockLog::create([
                    'menu_id' => $menu->id,
                    'item_name' => $menu->name,
                    'type' => 'keluar',
                    'quantity' => (int) $item->quantity,
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'notes' => 'Terjual pada pesanan '.$lockedOrder->order_code,
                    'actor' => $actor->name,
                ]);
            } else {
                $menu->increment('sold_count', (int) $item->quantity);
            }
        }

        $lockedOrder->update(['stock_deducted' => true]);
    }

    /**
     * Kembalikan stok saat pesanan lunas dibatalkan atau direfund (tanpa duplikasi).
     */
    public function restoreStock(Order $order, User $actor, string $reason): void
    {
        $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();
        if (! $lockedOrder || ! $lockedOrder->stock_deducted || $lockedOrder->stock_returned) {
            return;
        }

        // 1. Kumpulkan bahan baku yang perlu dikembalikan
        $returnedStocks = [];
        foreach ($lockedOrder->items as $item) {
            if (! $item->menu_id) {
                continue;
            }

            $recipes = Recipe::where('menu_id', $item->menu_id)
                ->when($item->variant_id, fn ($q) => $q->where('menu_variant_id', $item->variant_id))
                ->get();

            if ($recipes->isEmpty() && $item->variant_id) {
                $recipes = Recipe::where('menu_id', $item->menu_id)
                    ->whereNull('menu_variant_id')
                    ->get();
            }

            foreach ($recipes as $recipe) {
                $qty = (float) $recipe->amount * (int) $item->quantity;
                $returnedStocks[$recipe->stock_id] = ($returnedStocks[$recipe->stock_id] ?? 0.0) + $qty;
            }
        }

        // 2. Kembalikan stok bahan baku & catat StockMovement
        foreach ($returnedStocks as $stockId => $qty) {
            $stock = Stock::where('id', $stockId)->lockForUpdate()->first();
            if (! $stock) {
                continue;
            }

            $newQty = (float) $stock->quantity + $qty;
            $stock->update(['quantity' => $newQty]);

            StockMovement::create([
                'stock_id' => $stock->id,
                'user_id' => $actor->id,
                'type' => 'masuk',
                'quantity' => $qty,
                'stock_after' => $newQty,
                'note' => "Pengembalian stok pesanan {$lockedOrder->order_code} (dibatalkan: {$reason})",
            ]);

            $this->syncMenuAvailabilityForStock($stock);
        }

        // 3. Kembalikan stok menu jika ada
        foreach ($lockedOrder->items as $item) {
            if (! $item->menu_id) {
                continue;
            }

            $menu = Menu::where('id', $item->menu_id)->lockForUpdate()->first();
            if (! $menu) {
                continue;
            }

            if ($menu->stock !== null) {
                $before = (int) $menu->stock;
                $after = $before + (int) $item->quantity;

                $menu->update([
                    'stock' => $after,
                    'sold_count' => max(0, (int) $menu->sold_count - (int) $item->quantity),
                    'is_available' => $after > 0,
                ]);

                StockLog::create([
                    'menu_id' => $menu->id,
                    'item_name' => $menu->name,
                    'type' => 'masuk',
                    'quantity' => (int) $item->quantity,
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'notes' => 'Pengembalian stok pesanan '.$lockedOrder->order_code.' dibatalkan',
                    'actor' => $actor->name,
                ]);
            } else {
                $menu->decrement('sold_count', min((int) $menu->sold_count, (int) $item->quantity));
            }
        }

        $lockedOrder->update(['stock_returned' => true]);
    }

    public function markMenusUnavailableForStock(Stock $stock): void
    {
        $menuIds = Recipe::where('stock_id', $stock->id)->pluck('menu_id')->unique();
        if ($menuIds->isNotEmpty()) {
            Menu::whereIn('id', $menuIds)->update(['is_available' => false]);
        }
    }

    public function syncMenuAvailabilityForStock(Stock $stock): void
    {
        if ((float) $stock->quantity <= 0) {
            $this->markMenusUnavailableForStock($stock);

            return;
        }

        $menuIds = Recipe::where('stock_id', $stock->id)->pluck('menu_id')->unique();
        foreach ($menuIds as $menuId) {
            $menu = Menu::find($menuId);
            if (! $menu) {
                continue;
            }

            $allAvailable = Recipe::where('menu_id', $menuId)
                ->with('stock')
                ->get()
                ->every(fn ($r) => $r->stock && (float) $r->stock->quantity > 0);

            if ($allAvailable && ($menu->stock === null || $menu->stock > 0)) {
                $menu->update(['is_available' => true]);
            }
        }
    }
}
