<?php

namespace Tests\Feature\Integration;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebOrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_order_moves_through_full_lifecycle(): void
    {
        $kasir = User::factory()->kasir()->create();
        $this->actingAs($kasir);
        $this->openShift();

        $order = Order::factory()->web()->create([
            'order_status' => 'baru',
            'payment_status' => 'menunggu_pembayaran',
        ]);

        $this->post("/kasir/pesanan/{$order->id}/terima")->assertRedirect();
        $this->assertSame('diterima', $order->fresh()->order_status);
        $this->assertDatabaseHas('pesanan_riwayat', ['order_id' => $order->id, 'status' => 'DITERIMA']);

        foreach (['diproses', 'siap', 'selesai'] as $status) {
            $this->post("/kasir/pesanan/{$order->id}/status", ['status' => $status])->assertRedirect();
            $this->assertSame($status, $order->fresh()->order_status);
        }

        foreach (['DIPROSES', 'SIAP', 'SELESAI'] as $status) {
            $this->assertDatabaseHas('pesanan_riwayat', ['order_id' => $order->id, 'status' => $status]);
        }
    }

    public function test_offline_order_cannot_skip_to_invalid_status(): void
    {
        $kasir = User::factory()->kasir()->create();
        $this->actingAs($kasir);
        $this->openShift();

        $order = Order::factory()->create(['source' => 'offline', 'order_status' => 'diproses']);

        $this->post("/kasir/pesanan/{$order->id}/status", ['status' => 'baru'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('diproses', $order->fresh()->order_status);
    }
}
