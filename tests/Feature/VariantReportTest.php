<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariantReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_report_aggregates_parent_menu_with_variant_breakdown(): void
    {
        $menu = Menu::factory()->create(['name' => 'Vietnam Drip (Hot / Ice)']);
        $hot = MenuVariant::factory()->for($menu)->create(['name' => 'Hot', 'price' => 12000]);
        $ice = MenuVariant::factory()->for($menu)->create(['name' => 'Ice', 'size' => 'Large', 'price' => 15000]);

        $order = Order::factory()->paid()->create([
            'created_at' => now(),
            'subtotal' => 42000,
            'total' => 46200,
        ]);
        OrderItem::factory()->for($order)->create([
            'menu_id' => $menu->id,
            'variant_id' => $ice->id,
            'name' => 'Vietnam Drip - Ice - Large',
            'quantity' => 2,
            'subtotal' => 30000,
        ]);
        OrderItem::factory()->for($order)->create([
            'menu_id' => $menu->id,
            'variant_id' => $hot->id,
            'name' => 'Vietnam Drip - Hot',
            'quantity' => 1,
            'subtotal' => 12000,
        ]);

        $this->actingAsOwner()->get('/owner/laporan?periode=hari_ini')
            ->assertOk()
            ->assertViewHas('data', function ($data) use ($menu) {
                $row = $data['perMenu']->firstWhere('name', $menu->name);

                return $row !== null
                    && $row['quantity'] === 3
                    && (float) $row['revenue'] === 42000.0
                    && collect($row['variants'])->firstWhere('name', 'Ice - Large')['quantity'] === 2
                    && collect($row['variants'])->firstWhere('name', 'Hot')['quantity'] === 1;
            });
    }

    public function test_owner_dashboard_best_sellers_aggregate_by_parent_menu(): void
    {
        $menu = Menu::factory()->create(['name' => 'Teh Tarik (Hot / Ice)']);
        $hot = MenuVariant::factory()->for($menu)->create(['name' => 'Hot', 'price' => 8000]);
        $ice = MenuVariant::factory()->for($menu)->create(['name' => 'Ice', 'price' => 9000]);

        $order = Order::factory()->paid()->create(['created_at' => now()]);
        OrderItem::factory()->for($order)->create([
            'menu_id' => $menu->id,
            'variant_id' => $hot->id,
            'name' => 'Teh Tarik - Hot',
            'quantity' => 2,
        ]);
        OrderItem::factory()->for($order)->create([
            'menu_id' => $menu->id,
            'variant_id' => $ice->id,
            'name' => 'Teh Tarik - Ice',
            'quantity' => 3,
        ]);

        $this->actingAsOwner()->get('/owner?periode=hari_ini')
            ->assertOk()
            ->assertViewHas('topMenus', function ($topMenus) use ($menu) {
                $row = $topMenus->firstWhere('name', $menu->name);

                return $row !== null
                    && $row['quantity'] === 5
                    && collect($row['variants'])->sum('quantity') === 5;
            });
    }
}
