<?php

namespace Tests\Unit;

use App\Models\CashShift;
use App\Models\Order;
use App\Models\User;
use App\Services\ShiftService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ShiftServiceTest extends TestCase
{
    use RefreshDatabase;

    private ShiftService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ShiftService::class);
    }

    public function test_open_creates_active_shift(): void
    {
        $user = User::factory()->kasir()->create();

        $shift = $this->service->open($user, 150000);

        $this->assertSame('buka', $shift->status);
        $this->assertSame(150000.0, (float) $shift->expected_cash);
        $this->assertTrue($this->service->activeFor($user)->is($shift));
    }

    public function test_cannot_open_two_active_shifts(): void
    {
        $user = User::factory()->kasir()->create();
        $this->service->open($user, 100000);

        $this->expectException(ValidationException::class);
        $this->service->open($user, 200000);
    }

    public function test_summary_computes_expected_cash(): void
    {
        $user = User::factory()->kasir()->create();
        $shift = $this->service->open($user, 100000);

        Order::factory()->paid()->create([
            'cash_shift_id' => $shift->id,
            'payment_method' => 'tunai',
            'total' => 50000,
        ]);
        $this->service->recordMovement($shift, $user, 'masuk', 20000, 'Tambah kas');
        $this->service->recordMovement($shift, $user, 'keluar', 5000, 'Beli galon');

        $summary = $this->service->summary($shift);

        $this->assertSame(165000.0, (float) $summary['expected_cash']);
        $this->assertSame(50000.0, (float) $summary['cash_sales']);
        $this->assertSame(1, $summary['orders_count']);
    }

    public function test_close_sets_totals_and_cash_difference(): void
    {
        $user = User::factory()->kasir()->create();
        $shift = $this->service->open($user, 100000);

        Order::factory()->paid()->create([
            'cash_shift_id' => $shift->id,
            'payment_method' => 'tunai',
            'total' => 50000,
        ]);

        $closed = $this->service->close($shift, 145000);

        $this->assertSame('tutup', $closed->status);
        $this->assertSame(150000.0, (float) $closed->expected_cash);
        $this->assertSame(-5000.0, (float) $closed->cash_difference);
        $this->assertSame(1, $closed->total_transactions);
    }

    public function test_close_blocked_when_active_orders_exist(): void
    {
        $user = User::factory()->kasir()->create();
        $shift = $this->service->open($user, 100000);

        Order::factory()->create([
            'cash_shift_id' => $shift->id,
            'payment_status' => 'menunggu_pembayaran',
            'order_status' => 'diproses',
        ]);

        $this->expectException(ValidationException::class);
        $this->service->close($shift, 100000);
    }

    public function test_closed_shift_cannot_be_closed_again(): void
    {
        $user = User::factory()->kasir()->create();
        $shift = $this->service->open($user, 100000);
        $this->service->close($shift, 100000);

        $this->expectException(ValidationException::class);
        $this->service->close(CashShift::find($shift->id), 100000);
    }
}
