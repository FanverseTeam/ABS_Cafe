<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_order_with_phone_and_table(): void
    {
        $table = Table::factory()->create(['number' => 1, 'status' => 'tersedia']);
        $menu = Menu::factory()->create(['name' => 'Kopi Tubruk', 'price' => 15000, 'is_available' => true]);

        $payload = [
            'customer_name' => 'Budi',
            'customer_phone' => '08123456789',
            'order_type' => 'dine_in',
            'table_number' => 1,
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 2,
                    'catatan' => 'Gula sedikit',
                ],
            ],
        ];

        $response = $this->postJson(route('pelanggan.pesanan.store'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $orderCode = $response->json('order_code');
        $this->assertNotEmpty($orderCode);

        $order = Order::where('order_code', $orderCode)->first();
        $this->assertNotNull($order);
        $this->assertSame('Budi', $order->customer_name);
        $this->assertSame('08123456789', $order->customer_phone);
        $this->assertSame(1, $order->table_number);
        $this->assertSame('dine_in', $order->order_type);
        $this->assertSame('web', $order->source);
        $this->assertSame('menunggu_pembayaran', $order->payment_status);
        $this->assertSame('baru', $order->order_status);

        // Halaman nota customer sukses
        $viewResponse = $this->get(route('pelanggan.pesanan.sukses', $order->order_code));
        $viewResponse->assertOk()
            ->assertSee('Silakan Bayar di Kasir')
            ->assertSee($order->order_code)
            ->assertSee('Budi')
            ->assertSee('08123456789');

        // Endpoint status JSON customer
        $statusResponse = $this->getJson(route('pelanggan.pesanan.status', $order->order_code));
        $statusResponse->assertOk()
            ->assertJson([
                'success' => true,
                'payment_status' => 'menunggu_pembayaran',
            ]);
    }

    public function test_order_requires_customer_phone(): void
    {
        $menu = Menu::factory()->create(['name' => 'Espresso', 'price' => 15000, 'is_available' => true]);

        $payload = [
            'customer_name' => 'Tanpa Telepon',
            'order_type' => 'take_away',
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                ],
            ],
        ];

        $response = $this->postJson(route('pelanggan.pesanan.store'), $payload);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['customer_phone']);
    }

    public function test_customer_take_away_order(): void
    {
        $menu = Menu::factory()->create(['name' => 'Croissant', 'price' => 20000, 'is_available' => true]);

        $payload = [
            'customer_name' => 'Siti',
            'customer_phone' => '08987654321',
            'order_type' => 'take_away',
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                ],
            ],
        ];

        $response = $this->postJson(route('pelanggan.pesanan.store'), $payload);
        $response->assertOk()->assertJson(['success' => true]);

        $orderCode = $response->json('order_code');
        $order = Order::where('order_code', $orderCode)->first();
        $this->assertNotNull($order);
        $this->assertNull($order->table_number);
        $this->assertSame('take_away', $order->order_type);
    }

    public function test_customer_dine_in_without_table_number(): void
    {
        $menu = Menu::factory()->create(['name' => 'Americano', 'price' => 18000, 'is_available' => true]);

        $payload = [
            'customer_name' => 'Doni',
            'customer_phone' => '0811223344',
            'order_type' => 'dine_in',
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => 1,
                ],
            ],
        ];

        $response = $this->postJson(route('pelanggan.pesanan.store'), $payload);
        $response->assertOk()->assertJson(['success' => true]);

        $orderCode = $response->json('order_code');
        $order = Order::where('order_code', $orderCode)->first();
        $this->assertNotNull($order);
        $this->assertSame('dine_in', $order->order_type);
        $this->assertNull($order->table_number);
    }

    public function test_when_cashier_confirms_payment_order_status_advances_to_diproses(): void
    {
        $kasir = User::factory()->kasir()->create();
        $this->actingAs($kasir);
        $this->openShift();

        $menu = Menu::factory()->create(['name' => 'Latte', 'price' => 25000, 'is_available' => true]);

        $order = Order::factory()->web()->create([
            'customer_name' => 'Ahmad',
            'customer_phone' => '087700112233',
            'table_number' => 2,
            'order_type' => 'dine_in',
            'total' => 25000,
            'payment_status' => 'menunggu_pembayaran',
            'order_status' => 'baru',
        ]);

        // Kasir memproses pembayaran
        $response = $this->post("/kasir/pembayaran/{$order->id}/bayar", [
            'method' => 'tunai',
            'cash_received' => 50000,
        ]);

        $response->assertRedirect(route('kasir.payments.receipt', $order));

        $freshOrder = $order->fresh();
        $this->assertSame('lunas', $freshOrder->payment_status);
        $this->assertSame('diproses', $freshOrder->order_status);

        // Halaman web customer sekarang menampilkan Lunas & Pesanan Sedang Diproses
        $statusResponse = $this->getJson(route('pelanggan.pesanan.status', $order->order_code));
        $statusResponse->assertOk()
            ->assertJson([
                'success' => true,
                'payment_status' => 'lunas',
                'payment_status_label' => 'Lunas',
                'order_status' => 'diproses',
                'order_status_label' => 'Pesanan Sedang Diproses',
            ]);
    }
}
