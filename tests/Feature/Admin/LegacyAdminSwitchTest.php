<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\Concerns\RequiresLegacyAdmin;
use Tests\TestCase;

#[Group('legacy')]
class LegacyAdminSwitchTest extends TestCase
{
    use RefreshDatabase;
    use RequiresLegacyAdmin;

    public function test_legacy_dashboard_and_standalone_pages_are_active_when_switch_is_on(): void
    {
        $this->actingAsAdmin()->get('/admin')->assertOk();

        foreach ([
            '/admin/pesanan',
            '/admin/menu',
            '/admin/kategori',
            '/admin/stok',
            '/admin/promo',
            '/admin/pengguna',
            '/admin/meja',
            '/admin/pengaturan',
            '/admin/log',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_admin_sidebar_stays_at_seven_items_when_switch_is_on(): void
    {
        $response = $this->actingAsAdmin()->get('/admin');

        $response->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Manajemen User Staf & Hak Akses')
            ->assertSee('Manajemen Menu')
            ->assertSee('Meja')
            ->assertSee('Promo / Voucher')
            ->assertSee('Pengaturan Website')
            ->assertSee('Log Aktivitas')
            ->assertDontSee('Pengguna (Staf)')
            ->assertDontSee('Pengaturan Cafe');

        preg_match('/<nav class="flex-1.*?<\/nav>/s', $response->getContent(), $matches);

        $this->assertNotEmpty($matches);
        $this->assertSame(7, substr_count($matches[0], '<a href='));
    }

    public function test_legacy_order_widgets_return_on_dashboard_when_switch_is_on(): void
    {
        $this->actingAsAdmin()->get('/admin')
            ->assertOk()
            ->assertSee('Ringkasan Pesanan')
            ->assertSee('Pesanan Hari Ini')
            ->assertSee('Pesanan Terbaru')
            ->assertSee('Tren Penjualan')
            ->assertSee('/admin/pesanan', false);
    }
}
