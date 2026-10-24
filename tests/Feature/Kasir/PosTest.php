<?php

namespace Tests\Feature\Kasir;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    private function cart(Menu $menu, int $qty = 1): string
    {
        return json_encode([['menu_id' => $menu->id, 'quantity' => $qty]]);
    }

    public function test_pos_index_loads_with_active_shift(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $this->get('/kasir/pos')->assertOk();
    }

    public function test_pos_requires_active_shift(): void
    {
        $this->actingAsKasir()->get('/kasir/pos')
            ->assertRedirect(route('kasir.shift.open'));
    }

    public function test_totals_endpoint_computes_tax(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $menu = Menu::factory()->create(['price' => 20000, 'is_available' => true]);

        $this->postJson('/kasir/pos/totals', ['cart' => $this->cart($menu)])
            ->assertOk()
            ->assertJsonPath('subtotal', fn ($v) => (float) $v === 20000.0)
            ->assertJsonPath('discount', fn ($v) => (float) $v === 0.0)
            ->assertJsonPath('tax', fn ($v) => (float) $v === 2000.0)
            ->assertJsonPath('total', fn ($v) => (float) $v === 22000.0);
    }

    public function test_totals_applies_percentage_voucher(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $menu = Menu::factory()->create(['price' => 20000]);
        Promo::factory()->create(['code' => 'DISKON50', 'type' => 'persen', 'value' => 50]);

        $this->postJson('/kasir/pos/totals', [
            'cart' => $this->cart($menu),
            'voucher' => 'diskon50',
        ])->assertOk()
            ->assertJsonPath('discount', fn ($v) => (float) $v === 10000.0)
            ->assertJsonPath('total', fn ($v) => (float) $v === 11000.0);
    }

    public function test_totals_rejects_unknown_voucher(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $menu = Menu::factory()->create();

        $this->postJson('/kasir/pos/totals', [
            'cart' => $this->cart($menu),
            'voucher' => 'TIDAKADA',
        ])->assertStatus(422)->assertJsonPath('ok', false);
    }

    public function test_store_saves_held_order(): void
    {
        $this->actingAsKasir();
        $shift = $this->openShift();
        $menu = Menu::factory()->create(['price' => 25000]);

        $this->post('/kasir/pos/store', [
            'order_type' => 'take_away',
            'cart' => $this->cart($menu, 2),
            'action' => 'save',
        ])->assertRedirect(route('kasir.pos.index'));

        $this->assertDatabaseHas('pesanan', [
            'cash_shift_id' => $shift->id,
            'payment_status' => 'menunggu_pembayaran',
            'order_status' => 'diproses',
            'source' => 'offline',
        ]);
        $this->assertDatabaseHas('pesanan_item', ['name' => $menu->name, 'quantity' => 2]);
    }

    public function test_store_with_pay_action_creates_paid_order(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $menu = Menu::factory()->create(['price' => 20000]);

        $response = $this->post('/kasir/pos/store', [
            'order_type' => 'take_away',
            'cart' => $this->cart($menu),
            'action' => 'pay',
            'payment_method' => 'tunai',
            'cash_received' => 50000,
        ]);

        $order = Order::where('source', 'offline')->latest('id')->first();
        $response->assertRedirect(route('kasir.payments.receipt', $order));
        $this->assertSame('lunas', $order->fresh()->payment_status);
        $this->assertDatabaseHas('pembayaran', ['order_id' => $order->id, 'method' => 'tunai', 'status' => 'lunas']);
    }

    public function test_dine_in_requires_table_number(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $menu = Menu::factory()->create();

        $this->post('/kasir/pos/store', [
            'order_type' => 'dine_in',
            'cart' => $this->cart($menu),
            'action' => 'save',
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('pesanan', 0);
    }

    public function test_store_rejects_empty_cart(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $this->post('/kasir/pos/store', [
            'order_type' => 'take_away',
            'cart' => '[]',
            'action' => 'save',
        ])->assertSessionHas('error');
    }

    public function test_dine_in_marks_table_occupied(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        $table = Table::factory()->create(['number' => 9, 'status' => 'tersedia']);
        $menu = Menu::factory()->create(['price' => 15000]);

        $this->post('/kasir/pos/store', [
            'order_type' => 'dine_in',
            'table_number' => 9,
            'cart' => $this->cart($menu),
            'action' => 'save',
        ])->assertRedirect();

        $this->assertSame('terpakai', $table->fresh()->status);
    }
}
