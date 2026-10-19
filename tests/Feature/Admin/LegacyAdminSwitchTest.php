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

    public function test_legacy_sidebar_is_restored_when_switch_is_on(): void
    {
        $response = $this->actingAsAdmin()->get('/admin');

        $response->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Pesanan')
            ->assertSee('Kategori')
            ->assertSee('Stok')
            ->assertSee('Pengguna (Staf)')
            ->assertSee('Pengaturan Cafe');

        preg_match('/<nav class="flex-1.*?<\/nav>/s', $response->getContent(), $matches);

        $this->assertNotEmpty($matches);
        $this->assertSame(10, substr_count($matches[0], '<a href='));
    }
}
