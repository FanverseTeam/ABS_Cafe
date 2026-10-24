<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function requireLegacyAdminDisabled(): void
    {
        if (config('features.legacy_admin')) {
            $this->markTestSkipped('This assertion applies when FEATURE_LEGACY_ADMIN is disabled.');
        }
    }

    private function requireLegacyAdminEnabled(): void
    {
        if (! config('features.legacy_admin')) {
            $this->markTestSkipped('Legacy admin routes are disabled. Set FEATURE_LEGACY_ADMIN=true to run this test.');
        }
    }

    public function test_admin_dashboard_loads_when_switch_is_off(): void
    {
        $this->requireLegacyAdminDisabled();

        $this->actingAsAdmin()->get('/admin')
            ->assertOk()
            ->assertSee('Total Menu')
            ->assertSee('Menu Stok Menipis/Habis')
            ->assertSee('Promo Aktif')
            ->assertSee('Staf Aktif')
            ->assertSee('Jumlah Meja')
            ->assertSee('Pintasan Cepat')
            ->assertSee('Tambah Menu')
            ->assertSee('Tambah Promo')
            ->assertSee('Tambah Staf')
            ->assertSee('Aktivitas Terbaru')
            ->assertSee('Peringatan Stok Menipis')
            ->assertDontSee('Pesanan Terbaru')
            ->assertDontSee('Pendapatan')
            ->assertDontSee('Menunggu Bayar')
            ->assertDontSee('Tren Penjualan');
    }

    public function test_dashboard_stats_come_from_active_features(): void
    {
        $this->requireLegacyAdminDisabled();

        $category = Category::factory()->create();
        Menu::factory()->for($category)->create(['stock' => 50, 'min_stock' => 10]);
        Menu::factory()->for($category)->lowStock()->create();

        Promo::factory()->create(['is_active' => true]);
        Promo::factory()->inactive()->create();

        User::factory()->count(2)->create(['status' => 'aktif']);
        User::factory()->inactive()->create();
        Table::factory()->count(3)->create();

        $this->actingAsAdmin();

        $this->get('/admin')
            ->assertOk()
            ->assertViewHas('totalMenus', 2)
            ->assertViewHas('lowStockCount', 1)
            ->assertViewHas('activePromoCount', 1)
            ->assertViewHas('activeStaffCount', 3)
            ->assertViewHas('totalTables', 3);
    }

    public function test_dashboard_shows_only_the_five_latest_activity_logs(): void
    {
        $this->requireLegacyAdminDisabled();

        foreach (range(0, 5) as $index) {
            $log = ActivityLog::create([
                'user_name' => 'Admin ABS',
                'role' => 'admin',
                'activity' => "Aktivitas {$index}",
                'activity_type' => 'sistem',
                'ip_address' => '127.0.0.1',
            ]);

            $log->forceFill(['created_at' => now()->subMinutes($index)])->save();
        }

        $response = $this->actingAsAdmin()->get('/admin');

        $response->assertOk()
            ->assertSee('Aktivitas 0')
            ->assertSee('Aktivitas 4')
            ->assertDontSee('Aktivitas 5');

        $this->assertCount(5, $response->viewData('recentActivities'));
    }

    public function test_dashboard_links_to_active_admin_pages_only_when_switch_is_off(): void
    {
        $this->requireLegacyAdminDisabled();

        $response = $this->actingAsAdmin()->get('/admin')->assertOk();

        $response->assertDontSee('/admin/pesanan', false)
            ->assertDontSee('/admin/menu"', false)
            ->assertDontSee('/admin/kategori', false)
            ->assertDontSee('/admin/stok', false)
            ->assertDontSee('/admin/pengguna', false)
            ->assertDontSee('/admin/meja', false)
            ->assertDontSee('/admin/pengaturan', false)
            ->assertDontSee('/admin/log"', false);

        preg_match_all('/href="([^"]+)"/', $response->getContent(), $matches);

        $links = collect($matches[1])
            ->map(fn (string $url) => parse_url($url, PHP_URL_PATH))
            ->filter(fn (?string $path) => is_string($path) && str_starts_with($path, '/admin'))
            ->unique()
            ->values();

        $this->assertNotEmpty($links);

        foreach ($links as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_legacy_order_widgets_return_when_switch_is_on(): void
    {
        $this->requireLegacyAdminEnabled();

        $this->actingAsAdmin()->get('/admin')
            ->assertOk()
            ->assertSee('Ringkasan Pesanan')
            ->assertSee('Pesanan Hari Ini')
            ->assertSee('Pesanan Terbaru')
            ->assertSee('Tren Penjualan')
            ->assertSee('Semua Pesanan');
    }

    public function test_revenue_today_sums_only_paid_orders_paid_today(): void
    {
        $this->requireLegacyAdminEnabled();

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
        $this->requireLegacyAdminDisabled();

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

    public function test_admin_dashboard_renders_welcome_banner_with_required_elements(): void
    {
        $this->requireLegacyAdminDisabled();

        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Budi Santoso',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk()
            ->assertSee(Carbon::now()->translatedFormat('l, j F Y'))
            ->assertSee('Selamat datang,')
            ->assertSee('Budi Santoso')
            ->assertSee('Ikhtisar operasional ABS Cafe Tulungagung hari ini')
            ->assertSee('Kelola Menu')
            ->assertSee(route('admin.catalog.index', ['tab' => 'menu']), false);
    }
}
