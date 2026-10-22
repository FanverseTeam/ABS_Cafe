<?php

namespace Tests\Feature\Owner;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_dashboard_loads(): void
    {
        $this->actingAsOwner()->get('/owner')->assertOk();
    }

    public function test_summary_only_counts_paid_orders_within_period(): void
    {
        Order::factory()->paid()->create(['total' => 200000, 'subtotal' => 180000, 'created_at' => now()]);
        Order::factory()->create(['total' => 999999, 'payment_status' => 'menunggu_pembayaran', 'created_at' => now()]);

        $response = $this->actingAsOwner()->get('/owner?periode=hari_ini');
        $response->assertOk();
        $response->assertViewHas('summary', fn ($summary) => (float) $summary['sales']['value'] === 200000.0);
    }

    public function test_low_stock_menu_is_surfaced(): void
    {
        Menu::factory()->lowStock()->create(['name' => 'Kopi Menipis']);

        $this->actingAsOwner()->get('/owner')
            ->assertOk()
            ->assertViewHas('lowStockMenus', fn ($menus) => $menus->pluck('name')->contains('Kopi Menipis'));
    }

    public function test_active_promos_are_listed(): void
    {
        Promo::factory()->create(['title' => 'Promo Aktif Sekarang', 'is_active' => true]);
        Promo::factory()->inactive()->create(['title' => 'Promo Mati']);

        $this->actingAsOwner()->get('/owner')
            ->assertOk()
            ->assertViewHas('activePromos', fn ($promos) => $promos->pluck('title')->contains('Promo Aktif Sekarang')
                && ! $promos->pluck('title')->contains('Promo Mati'));
    }

    public function test_period_change_is_reflected(): void
    {
        $old = Order::factory()->paid()->create(['total' => 50000, 'created_at' => now()->subDays(20)]);

        $response = $this->actingAsOwner()->get('/owner?periode=7_hari');
        $response->assertOk();
        $response->assertViewHas('summary', fn ($summary) => (float) $summary['sales']['value'] === 0.0);

        $this->assertNotNull($old);
    }
}
