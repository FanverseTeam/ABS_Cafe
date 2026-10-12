<?php

namespace Tests\Feature\Integration;

use App\Models\CashShift;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosShiftFlowTest extends TestCase
{
    use RefreshDatabase;

    private function cart(Menu $menu, int $qty = 1): string
    {
        return json_encode([['menu_id' => $menu->id, 'quantity' => $qty]]);
    }

    public function test_cash_sale_flow_reconciles_stock_and_shift_end_to_end(): void
    {
        $kasir = User::factory()->kasir()->create();
        $this->actingAs($kasir);

        $this->post('/kasir/shift/buka', ['opening_cash' => 100000])->assertRedirect();
        $shift = CashShift::where('user_id', $kasir->id)->where('status', 'buka')->firstOrFail();

        $menu = Menu::factory()->create(['price' => 20000, 'stock' => 10, 'sold_count' => 0]);

        $this->post('/kasir/pos/store', [
            'order_type' => 'take_away',
            'cart' => $this->cart($menu),
            'action' => 'pay',
            'payment_method' => 'tunai',
            'cash_received' => 50000,
        ])->assertRedirect();

        $order = Order::where('cash_shift_id', $shift->id)->firstOrFail();
        $this->assertSame('lunas', $order->payment_status);
        $this->assertSame(22000.0, (float) $order->total);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'method' => 'tunai', 'amount' => 22000]);

        $menu->refresh();
        $this->assertSame(9, $menu->stock);
        $this->assertSame(1, $menu->sold_count);
        $this->assertDatabaseHas('stock_logs', ['menu_id' => $menu->id, 'type' => 'keluar', 'stock_after' => 9]);

        $this->post("/kasir/pesanan/{$order->id}/status", ['status' => 'siap'])->assertRedirect();
        $this->post("/kasir/pesanan/{$order->id}/status", ['status' => 'selesai'])->assertRedirect();

        $this->post('/kasir/shift/tutup', ['closing_cash' => 120000])->assertRedirect();

        $shift->refresh();
        $this->assertSame('tutup', $shift->status);
        $this->assertSame(122000.0, (float) $shift->expected_cash);
        $this->assertSame(-2000.0, (float) $shift->cash_difference);
        $this->assertSame(22000.0, (float) $shift->total_sales);
        $this->assertSame(1, (int) $shift->total_transactions);
    }

    public function test_dine_in_order_pays_and_releases_table_end_to_end(): void
    {
        $kasir = User::factory()->kasir()->create();
        $this->actingAs($kasir);
        $this->openShift();

        $menu = Menu::factory()->create(['price' => 15000, 'stock' => 20, 'sold_count' => 0]);
        \App\Models\Table::factory()->create(['number' => 5, 'status' => 'tersedia']);

        $this->post('/kasir/pos/store', [
            'order_type' => 'dine_in',
            'table_number' => 5,
            'cart' => $this->cart($menu),
            'action' => 'save',
        ])->assertRedirect();

        $order = Order::latest('id')->firstOrFail();
        $this->assertSame('menunggu_pembayaran', $order->payment_status);
        $this->assertSame('terpakai', \App\Models\Table::where('number', 5)->firstOrFail()->status);

        $this->post("/kasir/pesanan/{$order->id}/status", ['status' => 'siap'])->assertRedirect();
        $this->assertSame('siap', $order->fresh()->order_status);

        $this->post("/kasir/pembayaran/{$order->id}/bayar", [
            'method' => 'qris',
            'reference' => 'REF-1',
        ])->assertRedirect(route('kasir.payments.receipt', $order));

        $this->assertSame('lunas', $order->fresh()->payment_status);
        $this->assertSame('tersedia', \App\Models\Table::where('number', 5)->firstOrFail()->status);
        $this->assertSame(19, $menu->fresh()->stock);
    }
}
