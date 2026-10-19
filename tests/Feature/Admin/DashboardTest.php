<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\Concerns\RequiresLegacyAdmin;
use Tests\TestCase;

#[Group('legacy')]
class DashboardTest extends TestCase
{
    use RefreshDatabase;
    use RequiresLegacyAdmin;

    public function test_admin_dashboard_loads(): void
    {
        $this->actingAsAdmin()->get('/admin')->assertOk();
    }

    public function test_revenue_today_sums_only_paid_orders_paid_today(): void
    {
        $today = now();
        $yesterday = now()->subDay();

        Order::factory()->paid()->create(['total' => 100000, 'paid_at' => $today, 'created_at' => $today]);
        Order::factory()->paid()->create(['total' => 50000, 'paid_at' => $yesterday, 'created_at' => $yesterday]);
        Order::factory()->create(['total' => 250000, 'payment_status' => 'menunggu_pembayaran', 'paid_at' => null]);

        $response = $this->actingAsAdmin()->get('/admin');
        $response->assertOk();
        $response->assertViewHas('revenueToday', fn ($value) => (float) $value === 100000.0);
    }

    public function test_low_stock_menus_are_listed(): void
    {
        $category = Category::factory()->create();
        Menu::factory()->for($category)->lowStock()->create(['name' => 'Kopi Menipis']);
        Menu::factory()->for($category)->create(['name' => 'Kopi Aman', 'stock' => 99, 'min_stock' => 10]);

        $response = $this->actingAsAdmin()->get('/admin');
        $response->assertOk();
        $response->assertViewHas('lowStockMenus', function ($menus) {
            return $menus->pluck('name')->contains('Kopi Menipis')
                && ! $menus->pluck('name')->contains('Kopi Aman');
        });
    }
}
