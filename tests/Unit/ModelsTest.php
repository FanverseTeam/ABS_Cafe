<?php

namespace Tests\Unit;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_stock_status_accessor(): void
    {
        $this->assertSame('aman', Menu::factory()->create(['stock' => 50, 'min_stock' => 10])->stock_status);
        $this->assertSame('menipis', Menu::factory()->create(['stock' => 5, 'min_stock' => 10])->stock_status);
        $this->assertSame('habis', Menu::factory()->create(['stock' => 0, 'min_stock' => 10])->stock_status);
    }

    public function test_user_role_helpers(): void
    {
        $admin = User::factory()->admin()->create();
        $kasir = User::factory()->kasir()->create();

        $this->assertSame('admin', $admin->primaryRole());
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($kasir->isAdmin());
        $this->assertSame('Kasir', $kasir->roleLabel());
    }

    public function test_user_initials(): void
    {
        $user = User::factory()->create(['name' => 'Ariel Surya']);

        $this->assertSame('AS', $user->initials());
    }

    public function test_order_status_and_payment_labels(): void
    {
        $order = Order::factory()->create([
            'order_type' => 'take_away',
            'payment_method' => 'qris',
            'order_status' => 'siap',
            'payment_status' => 'menunggu_pembayaran',
        ]);

        $this->assertSame('Take-away', $order->orderTypeLabel());
        $this->assertSame('QRIS', $order->paymentMethodLabel());
        $this->assertSame('Siap / Disajikan', $order->orderStatusBadge()['label']);
        $this->assertFalse($order->isPaid());
        $this->assertFalse($order->isCancelled());
    }

    public function test_cancelled_order_reports_cancelled_badge(): void
    {
        $order = Order::factory()->cancelled()->create();

        $this->assertTrue($order->isCancelled());
        $this->assertSame('Dibatalkan', $order->orderStatusBadge()['label']);
    }
}
