<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CustomerMenuPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_menu_page_returns_ok_for_guest(): void
    {
        $response = $this->get(route('pelanggan.menu'))->assertOk();

        $response->assertSee('Menu');
        $response->assertSee('Rasa Yang Selalu Bikin Rindu');
        $response->assertSee(route('pelanggan.beranda'));
        $response->assertSee('Semua');
    }

    public function test_categories_and_menus_match_database_and_reflect_changes(): void
    {
        $category = Category::factory()->create([
            'name' => 'Minuman Hangat Tradisional',
            'order' => 1,
            'is_active' => true,
        ]);

        $menu = Menu::factory()->create([
            'category_id' => $category->id,
            'name' => 'Wedang Jahe Madu Keraton',
            'price' => 15000,
            'is_available' => true,
            'is_best_seller' => true,
            'image' => 'https://example.com/wedang.jpg',
        ]);

        MenuVariant::factory()->create([
            'menu_id' => $menu->id,
            'name' => 'Porsi',
            'price' => 15000,
            'is_available' => true,
            'is_default' => true,
        ]);

        $response = $this->get(route('pelanggan.menu'))->assertOk();
        $response->assertSee('Minuman Hangat Tradisional');
        $response->assertSee('Wedang Jahe Madu Keraton');
        $response->assertSee('Rp 15.000');
        $response->assertSee('https://example.com/wedang.jpg');

        // Ubah harga dan nama di DB -> langsung ter-update di response
        $menu->update([
            'name' => 'Wedang Jahe Sereh Istimewa',
            'price' => 18000,
        ]);

        $responseAfterUpdate = $this->get(route('pelanggan.menu'))->assertOk();
        $responseAfterUpdate->assertSee('Wedang Jahe Sereh Istimewa');
        $responseAfterUpdate->assertDontSee('Wedang Jahe Madu Keraton');
    }

    public function test_inactive_menu_displays_habis_and_disabled_button(): void
    {
        $category = Category::factory()->create([
            'name' => 'Kopi Favorit',
            'order' => 1,
            'is_active' => true,
        ]);

        $menu = Menu::factory()->create([
            'category_id' => $category->id,
            'name' => 'Kopi Arabika Gayo Langka',
            'price' => 25000,
            'is_available' => false,
        ]);

        $response = $this->get(route('pelanggan.menu'))->assertOk();
        $response->assertSee('Kopi Arabika Gayo Langka');
        $response->assertSee('Habis');
        $response->assertSee('disabled', false);
    }

    public function test_menu_with_multiple_variants_displays_starting_price(): void
    {
        $category = Category::factory()->create([
            'name' => 'Coffee Blend',
            'order' => 1,
            'is_active' => true,
        ]);

        $menu = Menu::factory()->create([
            'category_id' => $category->id,
            'name' => 'Kopi Tarik Signature',
            'price' => 12000,
            'is_available' => true,
        ]);

        MenuVariant::factory()->create([
            'menu_id' => $menu->id,
            'name' => 'Hot',
            'price' => 12000,
            'is_available' => true,
            'is_default' => true,
        ]);

        MenuVariant::factory()->create([
            'menu_id' => $menu->id,
            'name' => 'Ice',
            'price' => 14000,
            'is_available' => true,
            'is_default' => false,
        ]);

        $response = $this->get(route('pelanggan.menu'))->assertOk();
        $response->assertSee('Kopi Tarik Signature');
        $response->assertSee('Mulai Rp 12.000');
    }

    public function test_search_query_filters_menus_and_shows_empty_state_when_not_found(): void
    {
        $category = Category::factory()->create(['is_active' => true]);

        Menu::factory()->create([
            'category_id' => $category->id,
            'name' => 'Es Cendol Durian',
            'is_available' => true,
        ]);

        Menu::factory()->create([
            'category_id' => $category->id,
            'name' => 'Nasi Goreng Spesial',
            'is_available' => true,
        ]);

        // Cari 'cendol'
        $responseFound = $this->get(route('pelanggan.menu', ['q' => 'cendol']))->assertOk();
        $responseFound->assertSee('Es Cendol Durian');
        $responseFound->assertDontSee('Nasi Goreng Spesial');

        // Cari yang tidak ada
        $responseEmpty = $this->get(route('pelanggan.menu', ['q' => 'menu_pasti_tidak_ada_xyz']))->assertOk();
        $responseEmpty->assertSee('Menu tidak ditemukan');
    }

    public function test_category_filter_shows_selected_category_only(): void
    {
        $cat1 = Category::factory()->create(['name' => 'Kopi Seduh', 'order' => 1, 'is_active' => true]);
        $cat2 = Category::factory()->create(['name' => 'Camilan Gurih', 'order' => 2, 'is_active' => true]);

        Menu::factory()->create([
            'category_id' => $cat1->id,
            'name' => 'Kopi Tubruk Manis',
            'is_available' => true,
        ]);

        Menu::factory()->create([
            'category_id' => $cat2->id,
            'name' => 'Tahu Walik Crispy',
            'is_available' => true,
        ]);

        $response = $this->get(route('pelanggan.menu', ['kategori' => $cat1->slug]))->assertOk();
        $response->assertSee('Kopi Tubruk Manis');
        $response->assertDontSee('Tahu Walik Crispy');
    }

    public function test_empty_categories_do_not_appear(): void
    {
        $emptyCat = Category::factory()->create([
            'name' => 'Kategori Kosong Tanpa Menu',
            'is_active' => true,
        ]);

        $nonEmptyCat = Category::factory()->create([
            'name' => 'Kategori Punya Menu',
            'is_active' => true,
        ]);

        Menu::factory()->create([
            'category_id' => $nonEmptyCat->id,
            'name' => 'Menu Ada',
            'is_available' => true,
        ]);

        $response = $this->get(route('pelanggan.menu'))->assertOk();
        $response->assertSee('Kategori Punya Menu');
        $response->assertDontSee('Kategori Kosong Tanpa Menu');
    }

    public function test_menu_page_does_not_contain_staff_or_admin_links(): void
    {
        $content = $this->get(route('pelanggan.menu'))->assertOk()->getContent();

        $this->assertStringNotContainsString('/staf', $content);
        $this->assertStringNotContainsString('/login', $content);
        $this->assertStringNotContainsString('/admin', $content);
        $this->assertStringNotContainsString('/owner', $content);
        $this->assertStringNotContainsString('/kasir', $content);
        $this->assertStringNotContainsString('Login Staf', $content);
    }

    public function test_menu_page_does_not_contain_emojis(): void
    {
        $content = $this->get(route('pelanggan.menu'))->assertOk()->getContent();

        // Regex untuk mendeteksi karakter emoji umum
        $hasEmoji = preg_match('/[\x{1F300}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F900}-\x{1F9FF}\x{1F1E0}-\x{1F1FF}]/u', $content);
        $this->assertSame(0, $hasEmoji, 'Halaman menu customer tidak boleh memuat emoji.');
    }

    public function test_floating_cart_bar_and_bottom_sheet_markup_exist(): void
    {
        $response = $this->get(route('pelanggan.menu'))->assertOk();

        // Memastikan elemen bottom sheet dan floating cart ada dalam view
        $response->assertSee('cartTotalItems', false);
        $response->assertSee('Lihat Keranjang');
        $response->assertSee(route('pelanggan.keranjang'));
        $response->assertSee('openBottomSheet', false);
        $response->assertSee('Tambah ke Keranjang');
    }
}
