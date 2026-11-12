<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariantReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_report_aggregates_parent_menu_with_variant_breakdown(): void
    {
        $category = Category::factory()->create();
        $hot = Menu::factory()->for($category)->variant('Vietnam Drip', 'Hot', 12000)->create();
        $ice = Menu::factory()->for($category)->variant('Vietnam Drip', 'Ice', 15000)->create();

        $order = Order::factory()->paid()->create([
            'created_at' => now(),
            'subtotal' => 42000,
            'total' => 46200,
        ]);
        OrderItem::factory()->for($order)->create([
            'menu_id' => $ice->id,
            'name' => $ice->name,
            'price' => 15000,
            'quantity' => 2,
            'subtotal' => 30000,
        ]);
        OrderItem::factory()->for($order)->create([
            'menu_id' => $hot->id,
            'name' => $hot->name,
            'price' => 12000,
            'quantity' => 1,
            'subtotal' => 12000,
        ]);

        $this->actingAsOwner()->get('/owner/laporan?periode=hari_ini')
            ->assertOk()
            ->assertViewHas('data', function ($data) {
                $row = $data['perMenu']->firstWhere('name', 'Vietnam Drip');

                return $row !== null
                    && $row['quantity'] === 3
                    && (float) $row['revenue'] === 42000.0
                    && collect($row['variants'])->firstWhere('name', 'Ice')['quantity'] === 2
                    && collect($row['variants'])->firstWhere('name', 'Hot')['quantity'] === 1;
            });
    }

    public function test_owner_dashboard_best_sellers_aggregate_by_parent_menu(): void
    {
        $category = Category::factory()->create();
        $hot = Menu::factory()->for($category)->variant('Teh Tarik', 'Hot', 8000)->create();
        $ice = Menu::factory()->for($category)->variant('Teh Tarik', 'Ice', 9000)->create();

        $order = Order::factory()->paid()->create(['created_at' => now()]);
        OrderItem::factory()->for($order)->create([
            'menu_id' => $hot->id,
            'name' => $hot->name,
            'price' => 8000,
            'quantity' => 2,
            'subtotal' => 16000,
        ]);
        OrderItem::factory()->for($order)->create([
            'menu_id' => $ice->id,
            'name' => $ice->name,
            'price' => 9000,
            'quantity' => 3,
            'subtotal' => 27000,
        ]);

        $this->actingAsOwner()->get('/owner?periode=hari_ini')
            ->assertOk()
            ->assertViewHas('topMenus', function ($topMenus) {
                $row = $topMenus->firstWhere('name', 'Teh Tarik');

                return $row !== null
                    && $row['quantity'] === 5
                    && collect($row['variants'])->sum('quantity') === 5;
            });
    }
}
