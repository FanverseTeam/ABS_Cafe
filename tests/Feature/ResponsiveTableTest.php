<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\CashShift;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponsiveTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_responsive_table_component_renders_mobile_card_and_desktop_cells_in_same_markup(): void
    {
        $view = $this->blade(
            <<<'BLADE'
            <x-responsive-table>
                <x-slot:headers>
                    <th>Nama</th>
                    <th>Nilai</th>
                </x-slot:headers>
                <x-responsive-table.row>
                    <x-slot:title>Item Test</x-slot:title>
                    <x-slot:subtitle>Subjudul Test</x-slot:subtitle>
                    <x-slot:badge><span>Aktif</span></x-slot:badge>
                    <x-slot:fields>
                        <x-responsive-table.field label="Harga" value="Rp 25.000" />
                    </x-slot:fields>
                    <x-slot:actions>
                        <button>Aksi</button>
                    </x-slot:actions>
                    <x-slot:desktop>
                        <td class="hidden md:table-cell">Item Test</td>
                        <td class="hidden md:table-cell">Rp 25.000</td>
                    </x-slot:desktop>
                </x-responsive-table.row>
            </x-responsive-table>
            BLADE
        );

        $view->assertSee('data-responsive-table', false);
        $view->assertSee('overflow-x-hidden md:overflow-x-auto', false);
        $view->assertSee('data-mobile-card', false);
        $view->assertSee('block md:hidden', false);
        $view->assertSee('hidden md:table-cell', false);
        $view->assertSee('Item Test');
        $view->assertSee('Subjudul Test');
        $view->assertSee('Harga');
        $view->assertSee('Rp 25.000');
    }

    public function test_admin_activity_log_renders_responsive_table(): void
    {
        $user = User::factory()->admin()->create();
        ActivityLog::create([
            'user_name' => $user->name,
            'role' => 'admin',
            'activity_type' => 'auth',
            'activity' => 'User logged in successfully',
            'ip_address' => '127.0.0.1',
        ]);

        $this->actingAs($user);
        $response = $this->get(route('admin.activity.index'));

        $response->assertOk();
        $response->assertSee('data-responsive-table', false);
        $response->assertSee('data-mobile-card', false);
        $response->assertSee('hidden md:table-cell', false);
        $response->assertSee('User logged in successfully');
    }

    public function test_admin_staff_renders_responsive_table(): void
    {
        $user = User::factory()->admin()->create(['name' => 'Staf Utama']);
        $this->actingAs($user);

        $response = $this->get(route('admin.staff.index'));

        $response->assertOk();
        $response->assertSee('data-responsive-table', false);
        $response->assertSee('data-mobile-card', false);
        $response->assertSee('hidden md:table-cell', false);
        $response->assertSee('Staf Utama');
    }

    public function test_admin_catalog_renders_responsive_table(): void
    {
        $user = User::factory()->admin()->create();
        $cat = Category::create(['name' => 'Kopi Pilihan', 'slug' => 'kopi-pilihan']);
        Menu::factory()->create(['name' => 'Espresso Mantap', 'category_id' => $cat->id]);

        $this->actingAs($user);
        $response = $this->get(route('admin.catalog.index'));

        $response->assertOk();
        $response->assertSee('data-responsive-table', false);
        $response->assertSee('data-mobile-card', false);
        $response->assertSee('hidden md:table-cell', false);
        $response->assertSee('Espresso Mantap');
    }

    public function test_owner_promos_renders_responsive_table(): void
    {
        $owner = User::factory()->owner()->create();
        Promo::factory()->create([
            'title' => 'Diskon Super Hemat',
            'is_active' => true,
        ]);

        $this->actingAs($owner);
        $response = $this->get(route('owner.promos.index'));

        $response->assertOk();
        $response->assertSee('data-responsive-table', false);
        $response->assertSee('data-mobile-card', false);
        $response->assertSee('hidden md:table-cell', false);
        $response->assertSee('Diskon Super Hemat');
    }

    public function test_owner_cashiers_renders_responsive_table(): void
    {
        $owner = User::factory()->owner()->create();
        User::factory()->kasir()->create(['name' => 'Kasir Handal']);

        $this->actingAs($owner);
        $response = $this->get(route('owner.cashiers.index'));

        $response->assertOk();
        $response->assertSee('data-responsive-table', false);
        $response->assertSee('data-mobile-card', false);
        $response->assertSee('hidden md:table-cell', false);
        $response->assertSee('Kasir Handal');
    }

    public function test_owner_reports_renders_responsive_table(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner);
        $response = $this->get(route('owner.reports.index'));

        $response->assertOk();
        $response->assertSee('data-responsive-table', false);
        $response->assertSee('Detail Pesanan');
    }

    public function test_kasir_payments_renders_responsive_table(): void
    {
        $kasir = User::factory()->kasir()->create();
        CashShift::create([
            'user_id' => $kasir->id,
            'opened_at' => now(),
            'opening_cash' => 100000,
            'status' => 'buka',
        ]);

        $this->actingAs($kasir);
        $response = $this->get(route('kasir.payments.index'));

        $response->assertOk();
        $response->assertSee('data-responsive-table', false);
        $response->assertSee('Pembayaran Hari Ini');
    }

    public function test_kasir_shifts_renders_responsive_table(): void
    {
        $kasir = User::factory()->kasir()->create();
        CashShift::create([
            'user_id' => $kasir->id,
            'opened_at' => now()->subHours(8),
            'closed_at' => now(),
            'opening_cash' => 100000,
            'closing_cash' => 250000,
            'expected_cash' => 250000,
            'cash_difference' => 0,
            'total_sales' => 150000,
            'status' => 'tutup',
        ]);

        $this->actingAs($kasir);
        $response = $this->get(route('kasir.shift.index'));

        $response->assertOk();
        $response->assertSee('data-responsive-table', false);
        $response->assertSee('data-mobile-card', false);
        $response->assertSee('hidden md:table-cell', false);
    }
}
