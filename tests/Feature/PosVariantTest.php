<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosVariantTest extends TestCase
{
    use RefreshDatabase;

    private function parentMenu(): array
    {
        $menu = Menu::factory()->create([
            'name' => 'Vietnam Drip (Hot / Ice)',
            'price' => 12000,
        ]);
        $hot = MenuVariant::factory()->for($menu)->create([
            'name' => 'Hot',
            'size' => null,
            'price' => 12000,
            'is_available' => true,
            'sort_order' => 0,
        ]);
        $ice = MenuVariant::factory()->for($menu)->create([
            'name' => 'Ice',
            'size' => 'Large',
            'price' => 15000,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        return [$menu, $hot, $ice];
    }

    public function test_totals_use_the_selected_variant_price(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        [$menu, , $ice] = $this->parentMenu();

        $this->postJson('/kasir/pos/totals', [
            'cart' => json_encode([[
                'menu_id' => $menu->id,
                'variant_id' => $ice->id,
                'quantity' => 2,
            ]]),
        ])->assertOk()
            ->assertJsonPath('subtotal', fn ($value) => (float) $value === 30000.0);
    }

    public function test_unavailable_variant_cannot_be_sold(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        [$menu, , $ice] = $this->parentMenu();
        $ice->update(['is_available' => false]);

        $this->postJson('/kasir/pos/totals', [
            'cart' => json_encode([[
                'menu_id' => $menu->id,
                'variant_id' => $ice->id,
                'quantity' => 1,
            ]]),
        ])->assertStatus(422)
            ->assertJsonPath('ok', false);
    }

    public function test_pos_order_stores_variant_snapshot(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        [$menu, , $ice] = $this->parentMenu();

        $this->post('/kasir/pos/store', [
            'order_type' => 'take_away',
            'customer_name' => 'Budi',
            'cart' => json_encode([[
                'menu_id' => $menu->id,
                'variant_id' => $ice->id,
                'quantity' => 1,
            ]]),
            'action' => 'pay',
            'payment_method' => 'tunai',
            'cash_received' => 50000,
        ])->assertRedirect();

        $order = Order::latest('id')->firstOrFail();
        $item = $order->items()->firstOrFail();

        $this->assertSame($ice->id, $item->variant_id);
        $this->assertSame('Vietnam Drip - Ice - Large', $item->name);
        $this->assertSame(15000.0, (float) $item->price);
    }
}
