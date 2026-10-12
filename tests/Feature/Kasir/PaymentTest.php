<?php

namespace Tests\Feature\Kasir;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_index_loads(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $this->get('/kasir/pembayaran')->assertOk();
    }

    public function test_checkout_loads_for_unpaid_order(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->create(['payment_status' => 'menunggu_pembayaran']);

        $this->get("/kasir/pembayaran/{$order->id}/bayar")->assertOk();
    }

    public function test_checkout_redirects_when_already_paid(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->paid()->create();

        $this->get("/kasir/pembayaran/{$order->id}/bayar")
            ->assertRedirect(route('kasir.payments.receipt', $order));
    }

    public function test_pay_cash_marks_order_paid_and_returns_receipt(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->create(['total' => 22000, 'payment_status' => 'menunggu_pembayaran']);

        $this->post("/kasir/pembayaran/{$order->id}/bayar", [
            'method' => 'tunai',
            'cash_received' => 50000,
        ])->assertRedirect(route('kasir.payments.receipt', $order))
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertSame('lunas', $order->payment_status);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'method' => 'tunai',
            'amount' => 22000,
            'change_given' => 28000,
        ]);
    }

    public function test_pay_cash_rejects_insufficient_amount(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->create(['total' => 50000, 'payment_status' => 'menunggu_pembayaran']);

        $this->post("/kasir/pembayaran/{$order->id}/bayar", [
            'method' => 'tunai',
            'cash_received' => 10000,
        ])->assertRedirect()->assertSessionHas('error');

        $this->assertSame('menunggu_pembayaran', $order->fresh()->payment_status);
    }

    public function test_pay_non_cash_marks_order_paid(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->create(['total' => 30000, 'payment_status' => 'menunggu_pembayaran']);

        $this->post("/kasir/pembayaran/{$order->id}/bayar", [
            'method' => 'qris',
            'reference' => 'QR-123',
        ])->assertRedirect(route('kasir.payments.receipt', $order));

        $this->assertSame('lunas', $order->fresh()->payment_status);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'method' => 'qris', 'reference' => 'QR-123']);
    }

    public function test_paying_order_decrements_stock_and_writes_stock_log(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $menu = Menu::factory()->create(['stock' => 10, 'sold_count' => 0]);
        $order = Order::factory()->create(['payment_status' => 'menunggu_pembayaran', 'total' => 30000]);
        OrderItem::factory()->for($order)->create(['menu_id' => $menu->id, 'quantity' => 3, 'price' => 10000, 'subtotal' => 30000]);

        $this->post("/kasir/pembayaran/{$order->id}/bayar", ['method' => 'tunai', 'cash_received' => 100000]);

        $menu->refresh();
        $this->assertSame(7, $menu->stock);
        $this->assertSame(3, $menu->sold_count);
        $this->assertDatabaseHas('stock_logs', ['menu_id' => $menu->id, 'type' => 'keluar', 'stock_after' => 7]);
    }

    public function test_receipt_page_and_json_load(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->paid()->create();
        OrderItem::factory()->for($order)->create();

        $this->get("/kasir/pembayaran/{$order->id}/struk")->assertOk();

        $this->getJson("/kasir/pembayaran/{$order->id}/data")
            ->assertOk()
            ->assertJsonPath('code', $order->order_code);
    }
}
