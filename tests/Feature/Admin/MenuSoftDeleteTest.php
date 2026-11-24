<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\User;
use App\Support\MenuSalesReport;
use App\Support\PeriodFilter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_soft_delete_menu_via_catalog(): void
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->create('coffee.jpg', 100, 'image/jpeg');
        $imagePath = $image->store('menu', 'public');

        $category = Category::factory()->create();
        $menu = Menu::factory()->for($category)->create([
            'name' => 'Kopi Karamel Arsip',
            'image' => $imagePath,
            'price' => 25000,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->delete("/admin/katalog/menu/{$menu->id}");

        $response->assertRedirect(route('admin.catalog.index', ['tab' => 'menu']));
        $response->assertSessionHas('success');

        // Pastikan record di-soft-delete (deleted_at terisi, baris database tidak hilang)
        $this->assertSoftDeleted('menu', ['id' => $menu->id]);

        // File foto fisik di storage TIDAK boleh terhapus untuk kebutuhan rekap dan arsip
        Storage::disk('public')->assertExists($imagePath);

        // Pastikan tercatat di log aktivitas
        $this->assertDatabaseHas('log_aktivitas', [
            'activity_type' => 'menu',
        ]);
    }

    public function test_soft_deleted_menu_does_not_appear_on_customer_menu_page(): void
    {
        $category = Category::factory()->create(['name' => 'Coffee Series']);
        $activeMenu = Menu::factory()->for($category)->create([
            'name' => 'Kopi Aktif Segar',
            'is_available' => true,
        ]);
        $trashedMenu = Menu::factory()->for($category)->create([
            'name' => 'Kopi Dihapus Lama',
            'is_available' => true,
        ]);
        $trashedMenu->delete();

        $response = $this->get('/menu');
        $response->assertOk();
        $response->assertSee('Kopi Aktif Segar');
        $response->assertDontSee('Kopi Dihapus Lama');
    }

    public function test_soft_deleted_menu_does_not_appear_on_cashier_pos_page(): void
    {
        $category = Category::factory()->create();
        $activeMenu = Menu::factory()->for($category)->create([
            'name' => 'Latte Kasir Aktif',
            'is_available' => true,
        ]);
        $trashedMenu = Menu::factory()->for($category)->create([
            'name' => 'Latte Kasir Dihapus',
            'is_available' => true,
        ]);
        $trashedMenu->delete();

        $this->actingAsKasir();
        $this->openShift();

        $response = $this->get('/kasir/pos');
        $response->assertOk();
        $response->assertSee('Latte Kasir Aktif');
        $response->assertDontSee('Latte Kasir Dihapus');
    }

    public function test_customer_cannot_order_soft_deleted_menu(): void
    {
        $category = Category::factory()->create();
        $trashedMenu = Menu::factory()->for($category)->create([
            'name' => 'Menu Terhapus Dipesan',
            'price' => 20000,
            'is_available' => true,
        ]);
        $trashedMenu->delete();

        Table::factory()->create(['number' => 5, 'status' => 'tersedia']);

        $payload = [
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'order_type' => 'dine_in',
            'table_number' => 5,
            'items' => [
                [
                    'menu_id' => $trashedMenu->id,
                    'jumlah' => 1,
                ],
            ],
        ];

        $response = $this->postJson(route('pelanggan.pesanan.store'), $payload);

        // Harus gagal/error karena menu tidak ditemukan dalam scope aktif
        $this->assertTrue(
            $response->status() === 422 || $response->status() === 400 || $response->json('success') === false,
            'Customer order with soft-deleted menu must be rejected.'
        );
    }

    public function test_recap_and_order_item_relationship_preserves_soft_deleted_menu(): void
    {
        $category = Category::factory()->create();
        $menu = Menu::factory()->for($category)->create([
            'name' => 'Kopi Historis Rekap',
            'price' => 30000,
        ]);

        $order = Order::factory()->paid()->create([
            'created_at' => Carbon::now(),
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $menu->id,
            'name' => $menu->name,
            'quantity' => 2,
            'price' => 30000,
            'subtotal' => 60000,
        ]);

        // Menu kemudian di-soft-delete oleh admin
        $menu->delete();
        $this->assertSoftDeleted('menu', ['id' => $menu->id]);

        // Relasi OrderItem -> menu() harus tetap mengembalikan data menu karena withTrashed()
        $freshItem = OrderItem::find($orderItem->id);
        $this->assertNotNull($freshItem->menu);
        $this->assertSame('Kopi Historis Rekap', $freshItem->menu->name);

        // Laporan MenuSalesReport harus tetap merangkum data penjualan menu terhapus tersebut
        $period = PeriodFilter::fromRequest(new Request(['periode' => 'hari_ini']));
        $report = MenuSalesReport::forPeriod($period);

        $found = $report->firstWhere('name', 'Kopi Historis Rekap');
        $this->assertNotNull($found, 'Soft-deleted menu sales must still be counted in cafe recap report.');
        $this->assertEquals(2, $found['quantity']);
        $this->assertEquals(60000, $found['revenue']);
    }

    public function test_admin_can_filter_trashed_menus_and_restore_them(): void
    {
        $category = Category::factory()->create();
        $activeMenu = Menu::factory()->for($category)->create(['name' => 'Kopi Aktif']);
        $trashedMenu = Menu::factory()->for($category)->create(['name' => 'Kopi Terhapus Arsip']);
        $trashedMenu->delete();

        $admin = User::factory()->create(['role' => 'admin']);

        // Default view: hanya menampilkan yang aktif
        $responseDefault = $this->actingAs($admin)->get('/admin/katalog?tab=menu');
        $responseDefault->assertOk();
        $responseDefault->assertSee('Kopi Aktif');
        $responseDefault->assertDontSee('Kopi Terhapus Arsip');

        // Filter status=trashed: hanya menampilkan yang di-soft-delete
        $responseTrashed = $this->actingAs($admin)->get('/admin/katalog?tab=menu&status=trashed');
        $responseTrashed->assertOk();
        $responseTrashed->assertSee('Kopi Terhapus Arsip');
        $responseTrashed->assertDontSee('Kopi Aktif');

        // Restore menu
        $responseRestore = $this->actingAs($admin)->post("/admin/katalog/menu/{$trashedMenu->id}/restore");
        $responseRestore->assertRedirect();
        $this->assertStringContainsString('/admin/katalog', (string) $responseRestore->headers->get('Location'));
        $this->assertStringContainsString('status=trashed', (string) $responseRestore->headers->get('Location'));
        $responseRestore->assertSessionHas('success');

        // Pastikan tidak lagi soft deleted
        $this->assertNotSoftDeleted('menu', ['id' => $trashedMenu->id]);
    }
}
