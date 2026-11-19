<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\FavoriteMenuService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DailyFavoriteMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_favorite_menu_service_ranks_strictly_by_paid_sales_and_ignores_manual_flag(): void
    {
        $category = Category::factory()->create(['name' => 'Signature Coffee']);

        $menuA = Menu::factory()->for($category)->create(['name' => 'Kopi A Paling Laris', 'price' => 20000, 'is_available' => true, 'is_best_seller' => false]);
        $menuB = Menu::factory()->for($category)->create(['name' => 'Kopi B Laris Dua', 'price' => 22000, 'is_available' => true, 'is_best_seller' => false]);
        $menuC = Menu::factory()->for($category)->create(['name' => 'Kopi C Laris Tiga', 'price' => 25000, 'is_available' => true, 'is_best_seller' => false]);
        $menuD = Menu::factory()->for($category)->create(['name' => 'Kopi D Manual Best Seller', 'price' => 30000, 'is_available' => true, 'is_best_seller' => true]);

        // Buat pesanan berstatus lunas
        $order = Order::factory()->paid()->create();

        // Menu A terjual 10
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $menuA->id, 'name' => $menuA->name, 'quantity' => 10, 'price' => 20000, 'subtotal' => 200000]);
        // Menu B terjual 6
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $menuB->id, 'name' => $menuB->name, 'quantity' => 6, 'price' => 22000, 'subtotal' => 132000]);
        // Menu C terjual 3
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $menuC->id, 'name' => $menuC->name, 'quantity' => 3, 'price' => 25000, 'subtotal' => 75000]);
        // Menu D tidak memiliki penjualan sama sekali, meskipun di-centang manual is_best_seller = true

        $favorites = FavoriteMenuService::calculateTopFavorites(3);

        $this->assertCount(3, $favorites);
        $this->assertSame($menuA->id, $favorites[0]->id);
        $this->assertSame($menuB->id, $favorites[1]->id);
        $this->assertSame($menuC->id, $favorites[2]->id);

        // Menu D tidak boleh masuk karena penjualan terbanyak murni dari data transaksi riil
        $this->assertFalse($favorites->contains('id', $menuD->id));
    }

    public function test_soft_deleted_and_unavailable_menus_are_excluded(): void
    {
        $category = Category::factory()->create();

        $menuDeleted = Menu::factory()->for($category)->create(['name' => 'Kopi Terhapus', 'is_available' => true]);
        $menuHabis = Menu::factory()->for($category)->create(['name' => 'Kopi Habis', 'is_available' => false]);
        $menuAktif = Menu::factory()->for($category)->create(['name' => 'Kopi Aktif', 'is_available' => true]);

        $order = Order::factory()->paid()->create();
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $menuDeleted->id, 'name' => $menuDeleted->name, 'quantity' => 50, 'price' => 20000, 'subtotal' => 1000000]);
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $menuHabis->id, 'name' => $menuHabis->name, 'quantity' => 40, 'price' => 20000, 'subtotal' => 800000]);
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $menuAktif->id, 'name' => $menuAktif->name, 'quantity' => 5, 'price' => 20000, 'subtotal' => 100000]);

        $menuDeleted->delete();

        $favorites = FavoriteMenuService::calculateTopFavorites(3);

        $this->assertTrue($favorites->contains('id', $menuAktif->id));
        $this->assertFalse($favorites->contains('id', $menuDeleted->id));
        $this->assertFalse($favorites->contains('id', $menuHabis->id));
    }

    public function test_favorites_are_cached_with_daily_key(): void
    {
        $category = Category::factory()->create();
        Menu::factory()->count(3)->for($category)->create(['is_available' => true]);

        $cacheKey = 'daily_top_favorites_'.Carbon::today()->format('Y-m-d').'_3';
        $this->assertFalse(Cache::has($cacheKey));

        $favorites = FavoriteMenuService::getDailyTopFavorites(3);
        $this->assertCount(3, $favorites);
        $this->assertTrue(Cache::has($cacheKey));
    }

    public function test_landing_page_renders_best_of_taste_section_with_3_top_menus(): void
    {
        $category = Category::factory()->create();
        $menu1 = Menu::factory()->for($category)->create(['name' => 'Caramel Signature Cold', 'is_available' => true]);
        $menu2 = Menu::factory()->for($category)->create(['name' => 'Tenderloin Steak Premium', 'is_available' => true]);
        $menu3 = Menu::factory()->for($category)->create(['name' => 'Matcha Artisan Series', 'is_available' => true]);

        $order = Order::factory()->paid()->create();
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $menu1->id, 'name' => $menu1->name, 'quantity' => 15, 'price' => 25000, 'subtotal' => 375000]);
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $menu2->id, 'name' => $menu2->name, 'quantity' => 10, 'price' => 60000, 'subtotal' => 600000]);
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $menu3->id, 'name' => $menu3->name, 'quantity' => 8, 'price' => 28000, 'subtotal' => 224000]);

        $response = $this->get(route('pelanggan.beranda'));
        $response->assertOk();
        $response->assertSee('BEST OF TASTE');
        $response->assertSee('Caramel Signature Cold');
        $response->assertSee('Tenderloin Steak Premium');
        $response->assertSee('Matcha Artisan Series');
        $response->assertSee('LIHAT MENU');
    }

    public function test_customer_menu_page_rekomendasi_filter_uses_daily_top_favorites(): void
    {
        $category = Category::factory()->create();
        $topMenu = Menu::factory()->for($category)->create(['name' => 'Kopi Terlaris Nomor Satu', 'is_available' => true]);
        $regularMenu = Menu::factory()->for($category)->create(['name' => 'Kopi Biasa Jarang Dipesan', 'is_available' => true]);

        $order = Order::factory()->paid()->create();
        OrderItem::create(['order_id' => $order->id, 'menu_id' => $topMenu->id, 'name' => $topMenu->name, 'quantity' => 20, 'price' => 20000, 'subtotal' => 400000]);

        $response = $this->get(route('pelanggan.menu', ['kategori' => 'rekomendasi']));
        $response->assertOk();
        $response->assertSee('Kopi Terlaris Nomor Satu');
    }
}
