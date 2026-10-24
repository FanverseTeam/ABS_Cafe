<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\Concerns\RequiresLegacyAdmin;
use Tests\TestCase;

#[Group('legacy')]
class OrderTest extends TestCase
{
    use RefreshDatabase;
    use RequiresLegacyAdmin;

    public function test_order_index_loads_with_filters(): void
    {
        Order::factory()->create(['order_code' => '#ORD-00001', 'customer_name' => 'Andi']);
        Order::factory()->paid()->create(['order_code' => '#ORD-00002', 'customer_name' => 'Budi']);

        $this->actingAsAdmin()
            ->get('/admin/pesanan?search=Andi')
            ->assertOk()
            ->assertSee('#ORD-00001')
            ->assertDontSee('#ORD-00002');

        $this->actingAsAdmin()->get('/admin/pesanan?payment_status=lunas')->assertOk();
        $this->actingAsAdmin()->get('/admin/pesanan?order_status=diproses')->assertOk();
        $this->actingAsAdmin()->get('/admin/pesanan?order_type=take_away')->assertOk();
    }

    public function test_order_show_returns_json_payload(): void
    {
        $order = Order::factory()->create();
        OrderItem::factory()->for($order)->create(['name' => 'Kopi Susu']);

        $response = $this->actingAsAdmin()->getJson("/admin/pesanan/{$order->id}");

        $response->assertOk()
            ->assertJsonPath('order_code', $order->order_code)
            ->assertJsonPath('items.0.name', 'Kopi Susu');
    }

    public function test_admin_can_update_order_status_and_it_is_logged(): void
    {
        $this->actingAsAdmin();
        $admin = auth()->user();
        $order = Order::factory()->create(['order_status' => 'diproses']);

        $this->post("/admin/pesanan/{$order->id}/status", ['order_status' => 'siap'])
            ->assertRedirect();

        $this->assertSame('siap', $order->fresh()->order_status);
        $this->assertDatabaseHas('pesanan_riwayat', ['order_id' => $order->id, 'status' => 'SIAP']);
        $this->assertDatabaseHas('log_aktivitas', ['activity_type' => 'pesanan', 'user_name' => $admin->name]);
    }

    public function test_update_order_status_validates_allowed_values(): void
    {
        $order = Order::factory()->create();

        $this->actingAsAdmin()
            ->post("/admin/pesanan/{$order->id}/status", ['order_status' => 'invalid'])
            ->assertSessionHasErrors('order_status');
    }

    public function test_admin_can_cancel_order_with_reason(): void
    {
        $order = Order::factory()->create();

        $this->actingAsAdmin()
            ->post("/admin/pesanan/{$order->id}/cancel", ['cancel_reason' => 'Pelanggan pergi'])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame('dibatalkan', $order->payment_status);
        $this->assertSame('dibatalkan', $order->order_status);
        $this->assertSame('Pelanggan pergi', $order->cancel_reason);
        $this->assertDatabaseHas('pesanan_riwayat', ['order_id' => $order->id, 'status' => 'DIBATALKAN']);
    }

    public function test_cancel_requires_reason_min_five_chars(): void
    {
        $order = Order::factory()->create();

        $this->actingAsAdmin()
            ->post("/admin/pesanan/{$order->id}/cancel", ['cancel_reason' => 'no'])
            ->assertSessionHasErrors('cancel_reason');

        $this->assertSame('diproses', $order->fresh()->order_status);
    }

    public function test_cancelling_order_releases_table_when_no_other_active_order(): void
    {
        $table = Table::factory()->occupied()->create(['number' => 7]);
        $order = Order::factory()->create(['table_number' => 7]);

        $this->actingAsAdmin()
            ->post("/admin/pesanan/{$order->id}/cancel", ['cancel_reason' => 'Batal total']);

        $this->assertSame('tersedia', $table->fresh()->status);
    }

    public function test_orders_can_be_exported_as_csv(): void
    {
        Order::factory()->create();

        $response = $this->actingAsAdmin()->get('/admin/pesanan/export');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }
}
