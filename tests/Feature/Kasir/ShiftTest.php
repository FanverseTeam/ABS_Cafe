<?php

namespace Tests\Feature\Kasir;

use App\Models\CashShift;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_shift_index_loads(): void
    {
        $this->actingAsKasir()->get('/kasir/shift')->assertOk();
    }

    public function test_open_page_loads_when_no_active_shift(): void
    {
        $this->actingAsKasir()->get('/kasir/shift/buka')->assertOk();
    }

    public function test_open_page_redirects_when_shift_already_active(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $this->get('/kasir/shift/buka')
            ->assertRedirect(route('kasir.shift.index'))
            ->assertSessionHas('error');
    }

    public function test_kasir_can_open_shift(): void
    {
        $kasir = User::factory()->kasir()->create();
        $this->actingAs($kasir);

        $this->post('/kasir/shift/buka', ['opening_cash' => 250000])
            ->assertRedirect(route('kasir.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('cash_shifts', [
            'user_id' => $kasir->id,
            'opening_cash' => 250000,
            'expected_cash' => 250000,
            'status' => 'buka',
        ]);
    }

    public function test_open_shift_requires_opening_cash(): void
    {
        $this->actingAsKasir()->post('/kasir/shift/buka', [])
            ->assertSessionHasErrors('opening_cash');
    }

    public function test_cannot_open_second_shift_while_active(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $this->post('/kasir/shift/buka', ['opening_cash' => 100000])
            ->assertSessionHasErrors('shift');

        $this->assertSame(1, CashShift::count());
    }

    public function test_close_page_loads_with_active_shift(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $this->get('/kasir/shift/tutup')->assertOk();
    }

    public function test_close_page_redirects_without_shift(): void
    {
        $this->actingAsKasir()->get('/kasir/shift/tutup')
            ->assertRedirect(route('kasir.shift.open'));
    }

    public function test_kasir_can_close_shift_and_cash_difference_is_computed(): void
    {
        $this->actingAsKasir();
        $shift = $this->openShift(['opening_cash' => 200000, 'expected_cash' => 200000]);

        $this->post('/kasir/shift/tutup', ['closing_cash' => 185000])
            ->assertRedirect(route('kasir.shift.receipt', $shift));

        $shift->refresh();
        $this->assertSame('tutup', $shift->status);
        $this->assertSame(200000.0, (float) $shift->expected_cash);
        $this->assertSame(-15000.0, (float) $shift->cash_difference);
    }

    public function test_shift_cannot_be_closed_with_active_orders(): void
    {
        $this->actingAsKasir();
        $shift = $this->openShift();

        Order::factory()->create([
            'cash_shift_id' => $shift->id,
            'payment_status' => 'menunggu_pembayaran',
            'order_status' => 'diproses',
        ]);

        $this->post('/kasir/shift/tutup', ['closing_cash' => 200000])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('buka', $shift->fresh()->status);
    }

    public function test_cash_movement_is_recorded_and_reflected_in_expected_cash(): void
    {
        $this->actingAsKasir();
        $shift = $this->openShift(['opening_cash' => 100000, 'expected_cash' => 100000]);

        $this->post('/kasir/shift/kas', [
            'type' => 'masuk',
            'amount' => 50000,
            'reason' => 'Tambah modal',
        ])->assertRedirect();

        $this->assertDatabaseHas('cash_movements', [
            'cash_shift_id' => $shift->id,
            'type' => 'masuk',
            'amount' => 50000,
        ]);

        $this->post('/kasir/shift/tutup', ['closing_cash' => 150000])->assertRedirect();

        $this->assertSame(150000.0, (float) $shift->fresh()->expected_cash);
    }

    public function test_cash_movement_requires_active_shift(): void
    {
        $this->actingAsKasir()->post('/kasir/shift/kas', [
            'type' => 'masuk',
            'amount' => 50000,
            'reason' => 'x',
        ])->assertRedirect(route('kasir.shift.open'));
    }

    public function test_receipt_loads_for_own_shift_only(): void
    {
        $this->actingAsKasir();
        $shift = $this->openShift();

        $this->get("/kasir/shift/{$shift->id}/struk")->assertOk();

        $other = User::factory()->kasir()->create();
        $otherShift = CashShift::factory()->create(['user_id' => $other->id]);

        $this->get("/kasir/shift/{$otherShift->id}/struk")->assertForbidden();
    }

    public function test_shift_history_loads_for_own_shift(): void
    {
        $this->actingAsKasir();
        $shift = $this->openShift();

        $this->get("/kasir/shift/{$shift->id}")->assertOk();
    }
}
