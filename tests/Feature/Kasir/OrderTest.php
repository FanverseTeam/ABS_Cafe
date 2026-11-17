<?php

namespace Tests\Feature\Kasir;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_index_loads(): void
    {
        $this->actingAsKasir();
        Order::factory()->create();

        $this->get('/kasir/pesanan')->assertOk();
        $this->get('/kasir/pesanan?tab=web')->assertOk();
        $this->get('/kasir/pesanan?tab=belum_bayar')->assertOk();
        $this->get('/kasir/pesanan?tab=selesai')->assertOk();
    }

    public function test_order_detail_returns_json(): void
    {
        $this->actingAsKasir();
        $order = Order::factory()->create();
        OrderItem::factory()->for($order)->create(['name' => 'Kopi Detail']);

        $this->getJson("/kasir/pesanan/{$order->id}/data")
            ->assertOk()
            ->assertJsonPath('order.code', $order->order_code)
            ->assertJsonPath('items.0.name', 'Kopi Detail');
    }

    public function test_kasir_can_accept_web_order(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->web()->create(['order_status' => 'baru']);

        $this->post("/kasir/pesanan/{$order->id}/terima")->assertRedirect();

        $this->assertSame('diterima', $order->fresh()->order_status);
        $this->assertDatabaseHas('pesanan_riwayat', ['order_id' => $order->id, 'status' => 'DITERIMA']);
    }

    public function test_kasir_can_advance_order_status(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->create(['order_status' => 'diproses']);

        $this->post("/kasir/pesanan/{$order->id}/status", ['status' => 'siap'])->assertRedirect();

        $this->assertSame('siap', $order->fresh()->order_status);
    }

    public function test_invalid_status_target_is_rejected(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->create(['order_status' => 'diproses']);

        $this->post("/kasir/pesanan/{$order->id}/status", ['status' => 'baru'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('diproses', $order->fresh()->order_status);
    }

    public function test_kasir_can_cancel_order(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->create();

        $this->post("/kasir/pesanan/{$order->id}/batal", ['cancel_reason' => 'Salah input'])->assertRedirect();

        $order->refresh();
        $this->assertSame('dibatalkan', $order->payment_status);
        $this->assertSame('dibatalkan', $order->order_status);
    }

    public function test_cancel_requires_reason(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->create();

        $this->post("/kasir/pesanan/{$order->id}/batal", [])->assertSessionHasErrors('cancel_reason');
    }

    public function test_transaction_routes_require_active_shift(): void
    {
        $this->actingAsKasir();
        $order = Order::factory()->create();

        $this->post("/kasir/pesanan/{$order->id}/status", ['status' => 'siap'])
            ->assertRedirect(route('kasir.shift.open'));
    }

    public function test_kasir_can_scan_qr_order_and_advances_status_to_diproses(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->web()->create([
            'order_status' => 'baru',
            'payment_status' => 'menunggu_pembayaran',
        ]);

        $response = $this->get('/kasir/pesanan/scan/'.urlencode($order->order_code));
        $response->assertRedirect(route('kasir.payments.checkout', $order));

        $order->refresh();
        $this->assertSame('diproses', $order->order_status);
        $this->assertDatabaseHas('pesanan_riwayat', [
            'order_id' => $order->id,
            'status' => 'DIPROSES',
        ]);
    }

    public function test_kasir_can_scan_qr_via_post_submit_json(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->web()->create([
            'order_status' => 'baru',
            'payment_status' => 'menunggu_pembayaran',
        ]);

        $response = $this->postJson('/kasir/pesanan/scan', [
            'code' => $order->order_code,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('order_code', $order->order_code)
            ->assertJsonPath('order_status', 'diproses');

        $this->assertSame('diproses', $order->fresh()->order_status);
    }

    public function test_customer_status_endpoint_reflects_is_processed(): void
    {
        $order = Order::factory()->web()->create([
            'order_status' => 'baru',
            'payment_status' => 'menunggu_pembayaran',
        ]);

        $this->getJson('/pesanan/status/'.urlencode($order->order_code))
            ->assertOk()
            ->assertJsonPath('order_status', 'baru')
            ->assertJsonPath('order_status_label', 'Menunggu')
            ->assertJsonPath('is_processed', false);

        $order->update(['order_status' => 'diproses']);

        $this->getJson('/pesanan/status/'.urlencode($order->order_code))
            ->assertOk()
            ->assertJsonPath('order_status', 'diproses')
            ->assertJsonPath('is_processed', true);
    }

    public function test_unscanned_web_orders_do_not_appear_in_kasir_order_list_until_scanned(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $order = Order::factory()->web()->create([
            'order_code' => '#ORD-UNSCANNED-999',
            'order_status' => 'baru',
            'payment_status' => 'menunggu_pembayaran',
        ]);

        // Sebelum di-scan, tidak muncul di tab aktif maupun tab web
        $this->get('/kasir/pesanan')->assertDontSee('#ORD-UNSCANNED-999');
        $this->get('/kasir/pesanan?tab=web')->assertDontSee('#ORD-UNSCANNED-999');
        $this->get('/kasir/pembayaran')->assertDontSee('#ORD-UNSCANNED-999');

        // Kasir scan QR code pesanan
        $this->get('/kasir/pesanan/scan/'.urlencode($order->order_code));

        // Setelah di-scan, status menjadi diproses dan muncul di daftar pesanan kasir
        $this->assertSame('diproses', $order->fresh()->order_status);
        $this->get('/kasir/pesanan')->assertSee('#ORD-UNSCANNED-999');
        $this->get('/kasir/pesanan?tab=web')->assertSee('#ORD-UNSCANNED-999');
        $this->get('/kasir/pembayaran')->assertSee('#ORD-UNSCANNED-999');
        $this->get('/kasir/pembayaran')->assertSee('#ORD-UNSCANNED-999');
    }
}
