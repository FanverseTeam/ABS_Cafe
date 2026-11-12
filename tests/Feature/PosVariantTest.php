<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosVariantTest extends TestCase
{
    use RefreshDatabase;

    private function parentMenu(): array
    {
        $category = Category::factory()->create();
        $hot = Menu::factory()->for($category)->variant('Vietnam Drip', 'Hot', 12000)->create();
        $ice = Menu::factory()->for($category)->variant('Vietnam Drip', 'Ice', 15000)->create();

        return [$hot, $ice];
    }

    public function test_totals_use_the_selected_variant_price(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        [$hot, $ice] = $this->parentMenu();

        $this->postJson('/kasir/pos/totals', [
            'cart' => json_encode([[
                'menu_id' => $ice->id,
                'quantity' => 2,
            ]]),
        ])->assertOk()
            ->assertJsonPath('subtotal', fn ($value) => (float) $value === 30000.0);
    }

    public function test_unavailable_variant_cannot_be_sold(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        [$hot, $ice] = $this->parentMenu();
        $ice->update(['is_available' => false]);

        $this->postJson('/kasir/pos/totals', [
            'cart' => json_encode([[
                'menu_id' => $ice->id,
                'quantity' => 1,
            ]]),
        ])->assertStatus(422)
            ->assertJsonPath('ok', false);
    }

    public function test_pos_order_stores_variant_snapshot(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        [$hot, $ice] = $this->parentMenu();

        $this->post('/kasir/pos/store', [
            'order_type' => 'take_away',
            'customer_name' => 'Budi',
            'cart' => json_encode([[
                'menu_id' => $ice->id,
                'quantity' => 1,
            ]]),
            'action' => 'pay',
            'payment_method' => 'tunai',
            'cash_received' => 50000,
        ])->assertRedirect();

        $order = Order::latest('id')->firstOrFail();
        $item = $order->items()->firstOrFail();

        $this->assertSame($ice->id, $item->menu_id);
        $this->assertSame('Vietnam Drip (Ice)', $item->name);
        $this->assertSame(15000.0, (float) $item->price);
    }
}
