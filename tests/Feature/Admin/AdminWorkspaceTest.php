<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function requireLegacyAdminDisabled(): void
    {
        if (config('features.legacy_admin')) {
            $this->markTestSkipped('This assertion applies when FEATURE_LEGACY_ADMIN is disabled.');
        }
    }

    public function test_admin_sidebar_has_exactly_seven_active_features_and_no_legacy_navigation(): void
    {
        $this->requireLegacyAdminDisabled();

        $response = $this->actingAsAdmin()->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Manajemen User Staf & Hak Akses')
            ->assertSee('Manajemen Menu')
            ->assertSee('Meja & Metode Pembayaran')
            ->assertSee('Promo / Voucher')
            ->assertSee('Pengaturan Website')
            ->assertSee('Log Aktivitas')
            ->assertDontSee('/admin/pesanan', false)
            ->assertDontSee('/admin/kategori', false)
            ->assertDontSee('/admin/stok', false)
            ->assertDontSee('/admin/pengguna', false)
            ->assertDontSee('/admin/meja', false)
            ->assertDontSee('/admin/pengaturan', false)
            ->assertDontSee('/admin/log"', false)
            ->assertDontSee('Cari pesanan, menu, atau meja', false)
            ->assertDontSee('Pemberitahuan Sistem', false);

        preg_match('/<nav class="flex-1.*?<\/nav>/s', $response->getContent(), $matches);

        $this->assertNotEmpty($matches);
        $this->assertSame(7, substr_count($matches[0], '<a href='));
    }

    public function test_mobile_bottom_nav_has_beranda_and_more_sheet(): void
    {
        $this->requireLegacyAdminDisabled();

        $this->actingAsAdmin()->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Beranda')
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee('toggleAdminMore()', false)
            ->assertSee('Pengaturan Website')
            ->assertSee('Log Aktivitas')
            ->assertSee('Keluar');
    }

    public function test_admin_dashboard_is_active_when_switch_is_off(): void
    {
        $this->requireLegacyAdminDisabled();

        $this->actingAsAdmin()->get('/admin')->assertOk();
    }

    public function test_legacy_admin_urls_return_404_when_switch_is_off(): void
    {
        $this->requireLegacyAdminDisabled();

        $this->actingAsAdmin();

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
            $this->get($url)->assertNotFound();
        }
    }

    public function test_admin_cannot_access_owner_or_kasir_areas(): void
    {
        $this->actingAsAdmin()
            ->get('/owner')
            ->assertForbidden();

        $this->actingAsAdmin()
            ->get('/kasir')
            ->assertForbidden();
    }

    public function test_staff_management_crud_works(): void
    {
        $this->actingAsAdmin();

        $this->post(route('admin.staff.store'), [
            'name' => 'Staf Baru',
            'username' => 'stafbaru',
            'email' => 'stafbaru@example.com',
            'password' => 'rahasia123',
            'role' => 'kasir',
            'phone' => '081200000000',
            'status' => 'aktif',
        ])->assertRedirect();

        $user = User::where('username', 'stafbaru')->firstOrFail();

        $this->post(route('admin.staff.update', $user->id), [
            'name' => 'Staf Diperbarui',
            'username' => 'stafbaru',
            'email' => 'stafbaru@example.com',
            'role' => 'kasir',
            'phone' => '081200000000',
            'status' => 'aktif',
        ])->assertRedirect();

        $this->assertDatabaseHas('pengguna', ['id' => $user->id, 'name' => 'Staf Diperbarui']);

        $this->post(route('admin.staff.toggle.status', $user->id))->assertRedirect();
        $this->assertSame('nonaktif', $user->fresh()->status);
    }

    public function test_catalog_menu_category_stock_and_supplier_work(): void
    {
        $this->actingAsAdmin();

        $this->post(route('admin.catalog.category.store'), [
            'name' => 'Kopi Spesial',
            'icon' => 'coffee',
            'description' => 'Kategori baru',
        ])->assertRedirect();

        $category = Category::where('name', 'Kopi Spesial')->firstOrFail();

        $this->post(route('admin.catalog.menu.store'), [
            'name' => 'Kopi Baru',
            'category_id' => $category->id,
            'price' => 25000,
            'stock' => 10,
            'min_stock' => 3,
            'unit' => 'cup',
        ])->assertRedirect();

        $menu = Menu::where('name', 'Kopi Baru')->firstOrFail();

        $this->post(route('admin.catalog.stock.adjust', $menu->id), [
            'type' => 'masuk',
            'quantity' => 5,
        ])->assertRedirect();

        $this->assertSame(15, $menu->fresh()->stock);

        $this->post(route('admin.catalog.supplier.store'), [
            'name' => 'Supplier Kopi',
            'contact_person' => 'Andi',
            'phone' => '08123456789',
            'email' => 'supplier@example.com',
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('pemasok', ['name' => 'Supplier Kopi']);

        $this->get(route('admin.catalog.index', ['tab' => 'stock']))
            ->assertOk()
            ->assertSee('Kopi Baru')
            ->assertSee('Supplier Kopi');
    }

    public function test_operations_tables_and_payment_methods_work(): void
    {
        $this->actingAsAdmin();

        $this->post(route('admin.operations.tables.store'), [
            'number' => 12,
            'capacity' => 4,
            'status' => 'tersedia',
            'location_notes' => 'Lantai 2',
        ])->assertRedirect();

        $this->assertDatabaseHas('meja', ['number' => 12]);

        $this->post(route('admin.operations.payments.store'), [
            'name' => 'QRIS Baru',
            'code' => 'QRIS Baru',
            'type' => 'qris',
            'description' => 'Scan QRIS',
            'sort_order' => 2,
            'is_active' => '1',
        ])->assertRedirect();

        $method = PaymentMethod::where('code', 'qris_baru')->firstOrFail();

        $this->post(route('admin.operations.payments.toggle.active', $method->id))->assertRedirect();
        $this->assertFalse($method->fresh()->is_active);

        $this->get(route('admin.operations.index', ['tab' => 'pembayaran']))
            ->assertOk()
            ->assertSee('QRIS Baru');
    }

    public function test_voucher_page_and_crud_work(): void
    {
        $this->actingAsAdmin();

        $this->post(route('admin.vouchers.store'), [
            'title' => 'Voucher Akhir Pekan',
            'code' => 'weekend10',
            'description' => 'Diskon 10 persen',
            'type' => 'persen',
            'value' => 10,
            'applies_to' => 'semua',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'min_spend' => 0,
        ])->assertRedirect();

        $this->assertDatabaseHas('promo', ['title' => 'Voucher Akhir Pekan', 'code' => 'WEEKEND10']);

        $this->get(route('admin.vouchers.index'))
            ->assertOk()
            ->assertSee('Voucher Akhir Pekan');
    }

    public function test_website_settings_update_works(): void
    {
        $this->actingAsAdmin();

        $this->post(route('admin.website.profile'), [
            'cafe_name' => 'ABS Cafe Baru',
            'cafe_tagline' => 'Tagline Baru',
            'cafe_logo' => 'images/logo.png',
            'cafe_address' => 'Alamat Baru',
            'cafe_phone' => '081200000000',
            'cafe_email' => 'halo@abscafe.com',
            'hours_weekday' => '08:00 - 22:00',
            'hours_weekend' => '08:00 - 23:00',
        ])->assertRedirect(route('admin.website.index', ['tab' => 'profil']));

        $this->assertDatabaseHas('pengaturan', ['key' => 'cafe_name', 'value' => 'ABS Cafe Baru']);
    }

    public function test_website_settings_tabs_structure(): void
    {
        $this->actingAsAdmin();

        $this->get(route('admin.website.index'))
            ->assertOk()
            ->assertSee('Profil Cafe')
            ->assertSee('Pajak &amp; Biaya', false)
            ->assertSee('Akun Saya')
            ->assertDontSee('Metode Pembayaran &amp; QRIS', false)
            ->assertDontSee('Format Nota &amp; Struk', false)
            ->assertDontSee('Tampilan &amp; Banner', false);
    }

    public function test_activity_log_filter_and_export_work(): void
    {
        $this->actingAsAdmin();

        ActivityLog::create([
            'user_name' => 'Admin ABS',
            'role' => 'admin',
            'activity' => 'Aktivitas Unik Sekali',
            'activity_type' => 'menu',
            'ip_address' => '127.0.0.1',
        ]);

        $this->get(route('admin.activity.index', ['search' => 'Unik Sekali']))
            ->assertOk()
            ->assertSee('Aktivitas Unik Sekali');

        $response = $this->get(route('admin.activity.export'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
    }

    public function test_active_pages_render_without_legacy_dashboard_widgets(): void
    {
        $this->requireLegacyAdminDisabled();

        $this->actingAsAdmin();

        foreach ([
            route('admin.dashboard'),
            route('admin.staff.index'),
            route('admin.catalog.index'),
            route('admin.operations.index'),
            route('admin.vouchers.index'),
            route('admin.website.index'),
            route('admin.activity.index'),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertDontSee('Pemberitahuan Sistem', false)
                ->assertDontSee('Cari pesanan, menu, atau meja', false)
                ->assertDontSee('Pesanan Terbaru', false)
                ->assertDontSee('Tren Penjualan', false);
        }
    }
}
