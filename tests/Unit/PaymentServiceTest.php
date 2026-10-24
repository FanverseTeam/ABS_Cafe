<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PaymentService::class);
    }

    public function test_cash_payment_records_change_and_marks_paid(): void
    {
        $cashier = User::factory()->kasir()->create();
        $order = Order::factory()->create(['total' => 20000, 'payment_status' => 'menunggu_pembayaran']);

        $payment = $this->service->record($order, $cashier, ['method' => 'tunai', 'cash_received' => 50000]);

        $this->assertSame('lunas', $order->fresh()->payment_status);
        $this->assertSame(30000.0, (float) $payment->change_given);
        $this->assertDatabaseHas('pembayaran', ['order_id' => $order->id, 'method' => 'tunai']);
    }

    public function test_cash_payment_rejects_insufficient_amount(): void
    {
        $cashier = User::factory()->kasir()->create();
        $order = Order::factory()->create(['total' => 50000, 'payment_status' => 'menunggu_pembayaran']);

        $this->expectException(ValidationException::class);
        $this->service->record($order, $cashier, ['method' => 'tunai', 'cash_received' => 10000]);
    }

    public function test_non_cash_payment_sets_cash_received_equal_total(): void
    {
        $cashier = User::factory()->kasir()->create();
        $order = Order::factory()->create(['total' => 35000, 'payment_status' => 'menunggu_pembayaran']);

        $payment = $this->service->record($order, $cashier, ['method' => 'qris']);

        $this->assertSame(35000.0, (float) $payment->cash_received);
        $this->assertSame(0.0, (float) $payment->change_given);
        $this->assertSame('lunas', $order->fresh()->payment_status);
    }

    public function test_cancelled_order_cannot_be_paid(): void
    {
        $cashier = User::factory()->kasir()->create();
        $order = Order::factory()->cancelled()->create();

        $this->expectException(ValidationException::class);
        $this->service->record($order, $cashier, ['method' => 'tunai', 'cash_received' => 999999]);
    }

    public function test_already_paid_order_cannot_be_paid_again(): void
    {
        $cashier = User::factory()->kasir()->create();
        $order = Order::factory()->paid()->create();

        $this->expectException(ValidationException::class);
        $this->service->record($order, $cashier, ['method' => 'tunai', 'cash_received' => 999999]);
    }
}
