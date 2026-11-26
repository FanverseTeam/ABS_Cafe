<?php

namespace Tests\Feature\Pelanggan;

use App\Models\CafeSetting;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelangganPromoTest extends TestCase
{
    use RefreshDatabase;

    private User $kasirUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasirUser = User::where('username', 'kasir')->first()
            ?? User::factory()->create([
                'username' => 'kasir_test',
                'role' => 'kasir',
                'name' => 'Kasir Utama',
            ]);

        CafeSetting::set('active_penjaga_kasir', 'Kasir Utama');
        CafeSetting::set('tax_percent', '10');
    }

    public function test_keranjang_page_loads_with_active_promos(): void
    {
        Promo::factory()->create([
            'title' => 'Promo Diskon 10rb',
            'code' => 'HEMAT10',
            'type' => 'nominal',
            'value' => 10000,
            'min_spend' => 30000,
            'is_active' => true,
        ]);

        $response = $this->get(route('pelanggan.keranjang'));
        $response->assertOk()
            ->assertSee('Promo &amp; Diskon', false)
            ->assertSee('Promo Diskon 10rb')
            ->assertSee('HEMAT10');
    }

    public function test_store_pesanan_auto_applies_best_eligible_promo(): void
    {
        $table = Table::factory()->create(['number' => 5]);
        $category = Category::factory()->create(['is_active' => true]);
        $menu = Menu::factory()->create([
            'category_id' => $category->id,
            'price' => 25000,
            'is_available' => true,
        ]);

        $promo = Promo::factory()->create([
            'title' => 'Diskon Pelanggan 15%',
            'code' => 'DISKON15',
            'type' => 'persen',
            'value' => 15,
            'min_spend' => 40000,
            'is_active' => true,
        ]);

        // Belanja 2 item @ 25.000 = subtotal 50.000 (memenuhi min_spend 40.000)
        // Diskon 15% dari 50.000 = 7.500
        // Pajak 10% dari (50.000 - 7.500) = 4.250
        // Total = 46.750
        $payload = [
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'order_type' => 'dine_in',
            'table_number' => 5,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 2,
                    'nama' => $menu->name,
                    'harga' => 25000,
                ],
            ],
        ];

        $response = $this->postJson(route('pelanggan.pesanan.store'), $payload);
        $response->assertOk()
            ->assertJsonPath('success', true);

        $order = Order::where('customer_name', 'Budi Santoso')->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertSame($promo->id, $order->promo_id);
        $this->assertEquals(7500, (float) $order->discount);
        $this->assertEquals(50000, (float) $order->subtotal);
        $this->assertEquals(46750, (float) $order->total);

        // Halaman pesanan sukses memuat nama promo & potongan
        $responseSukses = $this->get(route('pelanggan.pesanan.sukses', $order->order_code));
        $responseSukses->assertOk()
            ->assertSee('Diskon Pelanggan 15%')
            ->assertSee('7.500');
    }

    public function test_store_pesanan_does_not_apply_promo_if_subtotal_below_min_spend(): void
    {
        $table = Table::factory()->create(['number' => 2]);
        $category = Category::factory()->create(['is_active' => true]);
        $menu = Menu::factory()->create([
            'category_id' => $category->id,
            'price' => 20000,
            'is_available' => true,
        ]);

        Promo::factory()->create([
            'title' => 'Diskon Belanja Banyak',
            'code' => 'BANYAK',
            'type' => 'nominal',
            'value' => 10000,
            'min_spend' => 50000,
            'is_active' => true,
        ]);

        // Belanja hanya 1 item @ 20.000 (di bawah min_spend 50.000)
        $payload = [
            'customer_name' => 'Andi',
            'customer_phone' => '0899887766',
            'order_type' => 'take_away',
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                    'nama' => $menu->name,
                    'harga' => 20000,
                ],
            ],
        ];

        $response = $this->postJson(route('pelanggan.pesanan.store'), $payload);
        $response->assertOk()
            ->assertJsonPath('success', true);

        $order = Order::where('customer_name', 'Andi')->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertNull($order->promo_id);
        $this->assertEquals(0, (float) $order->discount);
        $this->assertEquals(20000, (float) $order->subtotal);
    }

    public function test_store_pesanan_respects_no_promo_flag(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $menu = Menu::factory()->create([
            'category_id' => $category->id,
            'price' => 60000,
            'is_available' => true,
        ]);

        Promo::factory()->create([
            'title' => 'Promo Otomatis',
            'type' => 'nominal',
            'value' => 10000,
            'min_spend' => 30000,
            'is_active' => true,
        ]);

        $payload = [
            'customer_name' => 'Citra',
            'customer_phone' => '0877112233',
            'order_type' => 'take_away',
            'no_promo' => true,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                    'nama' => $menu->name,
                    'harga' => 60000,
                ],
            ],
        ];

        $response = $this->postJson(route('pelanggan.pesanan.store'), $payload);
        $response->assertOk()
            ->assertJsonPath('success', true);

        $order = Order::where('customer_name', 'Citra')->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertNull($order->promo_id);
        $this->assertEquals(0, (float) $order->discount);
    }
}
