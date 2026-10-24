<?php

namespace Tests\Unit;

use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Table;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderService::class);
    }

    public function test_generate_code_uses_sequential_ord_prefix(): void
    {
        $code = $this->service->generateCode();
        $this->assertSame('#ORD-00001', $code);

        Order::factory()->create(['order_code' => '#ORD-00001']);
        $this->assertSame('#ORD-00002', $this->service->generateCode());
    }

    public function test_build_lines_uses_price_from_database(): void
    {
        $menu = Menu::factory()->create(['price' => 18000, 'stock' => 10, 'is_available' => true]);

        $built = $this->service->buildLines([['menu_id' => $menu->id, 'quantity' => 2]]);

        $this->assertSame(36000.0, $built['subtotal']);
        $this->assertSame(36000.0, $built['items'][0]['subtotal']);
    }

    public function test_build_lines_rejects_unavailable_menu(): void
    {
        $menu = Menu::factory()->unavailable()->create();

        $this->expectException(ValidationException::class);
        $this->service->buildLines([['menu_id' => $menu->id, 'quantity' => 1]]);
    }

    public function test_build_lines_rejects_insufficient_stock(): void
    {
        $menu = Menu::factory()->create(['stock' => 1, 'is_available' => true]);

        $this->expectException(ValidationException::class);
        $this->service->buildLines([['menu_id' => $menu->id, 'quantity' => 5]]);
    }

    public function test_build_lines_applies_variant_price_and_name(): void
    {
        $menu = Menu::factory()->create(['price' => 18000, 'name' => 'Kopi', 'stock' => 10]);
        $variant = MenuVariant::factory()->for($menu)->create(['name' => 'Large', 'price' => 25000]);

        $built = $this->service->buildLines([['menu_id' => $menu->id, 'variant_id' => $variant->id, 'quantity' => 1]]);

        $this->assertSame(25000.0, $built['items'][0]['price']);
        $this->assertSame('Kopi (Large)', $built['items'][0]['name']);
    }

    public function test_find_promo_returns_null_when_code_empty(): void
    {
        $this->assertNull($this->service->findPromo(null));
        $this->assertNull($this->service->findPromo(''));
    }

    public function test_find_promo_throws_for_unknown_code(): void
    {
        $this->expectException(ValidationException::class);
        $this->service->findPromo('TIDAKADA');
    }

    public function test_find_promo_throws_for_expired_promo(): void
    {
        Promo::factory()->expired()->create(['code' => 'EXPIRED', 'is_active' => true]);

        $this->expectException(ValidationException::class);
        $this->service->findPromo('EXPIRED');
    }

    public function test_calculate_discount_percentage(): void
    {
        $promo = Promo::factory()->create(['type' => 'persen', 'value' => 20, 'min_spend' => 0]);
        $items = [['menu_id' => 1, 'subtotal' => 50000.0, 'price' => 50000.0, 'quantity' => 1]];

        $result = $this->service->calculateDiscount($promo, $items, 50000.0);

        $this->assertSame(10000.0, $result['discount']);
    }

    public function test_calculate_discount_nominal_is_capped_at_eligible_subtotal(): void
    {
        $promo = Promo::factory()->create(['type' => 'nominal', 'value' => 100000, 'min_spend' => 0]);
        $items = [['menu_id' => 1, 'subtotal' => 30000.0, 'price' => 30000.0, 'quantity' => 1]];

        $result = $this->service->calculateDiscount($promo, $items, 30000.0);

        $this->assertSame(30000.0, $result['discount']);
    }

    public function test_calculate_discount_enforces_min_spend(): void
    {
        $promo = Promo::factory()->create(['type' => 'persen', 'value' => 10, 'min_spend' => 100000]);
        $items = [['menu_id' => 1, 'subtotal' => 50000.0, 'price' => 50000.0, 'quantity' => 1]];

        $this->expectException(ValidationException::class);
        $this->service->calculateDiscount($promo, $items, 50000.0);
    }

    public function test_buy_x_get_y_gives_cheapest_unit_free_per_three(): void
    {
        $promo = Promo::factory()->create(['type' => 'buy_x_get_y', 'value' => 100, 'min_spend' => 0]);
        $items = [['menu_id' => 1, 'subtotal' => 90000.0, 'price' => 30000.0, 'quantity' => 3]];

        $result = $this->service->calculateDiscount($promo, $items, 90000.0);

        $this->assertSame(30000.0, $result['discount']);
    }

    public function test_advance_status_follows_offline_flow(): void
    {
        $order = Order::factory()->create(['source' => 'offline', 'order_status' => 'diproses']);
        $user = User::factory()->kasir()->create();

        $this->service->advanceStatus($order, $user, 'siap');

        $this->assertSame('siap', $order->fresh()->order_status);
        $this->assertDatabaseHas('pesanan_riwayat', ['order_id' => $order->id, 'status' => 'SIAP']);
    }

    public function test_advance_status_rejects_invalid_target(): void
    {
        $order = Order::factory()->create(['source' => 'offline', 'order_status' => 'diproses']);
        $user = User::factory()->kasir()->create();

        $this->expectException(ValidationException::class);
        $this->service->advanceStatus($order, $user, 'baru');
    }

    public function test_cancel_releases_table_when_no_other_active_order(): void
    {
        $table = Table::factory()->create(['number' => 6, 'status' => 'terpakai']);
        $order = Order::factory()->create(['table_number' => 6]);
        $user = User::factory()->kasir()->create();

        $this->service->cancel($order, $user, 'Batal');

        $this->assertSame('dibatalkan', $order->fresh()->order_status);
        $this->assertSame('tersedia', $table->fresh()->status);
    }

    public function test_cancel_keeps_table_occupied_when_other_order_active(): void
    {
        $table = Table::factory()->create(['number' => 6, 'status' => 'terpakai']);
        $order = Order::factory()->create(['table_number' => 6]);
        Order::factory()->create(['table_number' => 6, 'order_status' => 'diproses', 'payment_status' => 'menunggu_pembayaran']);
        $user = User::factory()->kasir()->create();

        $this->service->cancel($order, $user, 'Batal');

        $this->assertSame('terpakai', $table->fresh()->status);
    }

    public function test_create_order_computes_totals_and_persists_items(): void
    {
        $menu = Menu::factory()->create(['price' => 20000, 'stock' => 10, 'is_available' => true]);
        $cashier = User::factory()->kasir()->create();

        $order = $this->service->createOrder([
            'items' => [['menu_id' => $menu->id, 'quantity' => 1]],
            'order_type' => 'take_away',
            'customer_name' => null,
            'voucher' => null,
            'source' => 'offline',
            'order_status' => 'diproses',
            'payment_method' => 'tunai',
        ], $cashier, null);

        $this->assertSame(20000.0, (float) $order->subtotal);
        $this->assertSame(2000.0, (float) $order->tax);
        $this->assertSame(22000.0, (float) $order->total);
        $this->assertSame('Pelanggan', $order->customer_name);
        $this->assertCount(1, $order->items);
        $this->assertDatabaseHas('pesanan_riwayat', ['order_id' => $order->id, 'status' => 'DIBUAT']);
    }
}
