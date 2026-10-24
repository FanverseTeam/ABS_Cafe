<?php

namespace Tests\Feature\Kasir;

use App\Models\CafeSetting;
use App\Models\CashAdjustment;
use App\Models\CashShift;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoShiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_shift_uses_default_opening_cash_from_setting(): void
    {
        CafeSetting::set('default_opening_cash', '125000');

        $kasir = User::factory()->kasir()->create();
        $this->actingAs($kasir);

        $response = $this->get('/kasir/shift/buka');
        $response->assertOk();
        $response->assertSee('125.000');
        $response->assertSee('Modal Awal Laci dari Pengaturan Bisnis');

        $this->post('/kasir/shift/buka', [])
            ->assertRedirect(route('kasir.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('shift', [
            'user_id' => $kasir->id,
            'opening_cash' => 125000,
            'status' => 'buka',
        ]);
    }

    public function test_next_shift_uses_closing_cash_from_previous_shift_across_cashiers(): void
    {
        $kasirA = User::factory()->kasir()->create(['name' => 'Kasir A']);
        $kasirB = User::factory()->kasir()->create(['name' => 'Kasir B']);

        // Kasir A membuka dan menutup shift
        $this->actingAs($kasirA);
        $this->post('/kasir/shift/buka', []);
        $shiftA = CashShift::where('user_id', $kasirA->id)->firstOrFail();

        // Buat order tunai lunas
        Order::factory()->paid()->create([
            'cash_shift_id' => $shiftA->id,
            'payment_method' => 'tunai',
            'total' => 60000,
        ]);

        // Tutup shift otomatis
        $this->post('/kasir/shift/tutup', [])
            ->assertRedirect(route('kasir.shift.receipt', $shiftA));

        $shiftA->refresh();
        $this->assertSame('tutup', $shiftA->status);
        $this->assertSame(160000.0, (float) $shiftA->closing_cash);
        $this->assertSame(0.0, (float) $shiftA->cash_difference);

        // Kasir B membuka shift berikutnya
        $this->actingAs($kasirB);
        $response = $this->get('/kasir/shift/buka');
        $response->assertOk();
        $response->assertSee('160.000');
        $response->assertSee('Kasir A');

        $this->post('/kasir/shift/buka', [])
            ->assertRedirect(route('kasir.dashboard'));

        $shiftB = CashShift::where('user_id', $kasirB->id)->latest('id')->firstOrFail();
        $this->assertSame(160000.0, (float) $shiftB->opening_cash);
        $this->assertStringContainsString('Kasir A', $shiftB->opening_cash_source);
    }

    public function test_auto_close_calculates_cash_correctly_including_movements_and_sets_zero_difference(): void
    {
        $kasir = User::factory()->kasir()->create();
        $this->actingAs($kasir);

        $this->post('/kasir/shift/buka', []);
        $shift = CashShift::where('user_id', $kasir->id)->firstOrFail();

        // Penjualan tunai
        Order::factory()->paid()->create([
            'cash_shift_id' => $shift->id,
            'payment_method' => 'tunai',
            'total' => 50000,
        ]);

        // Kas masuk manual
        $this->post('/kasir/shift/kas', [
            'type' => 'masuk',
            'amount' => 20000,
            'reason' => 'Tukar uang kecil',
        ]);

        // Kas keluar manual
        $this->post('/kasir/shift/kas', [
            'type' => 'keluar',
            'amount' => 5000,
            'reason' => 'Beli plastik',
        ]);

        // Tutup shift tanpa input closing_cash
        $this->post('/kasir/shift/tutup', [])
            ->assertRedirect(route('kasir.shift.receipt', $shift));

        $shift->refresh();
        // 100.000 (modal) + 50.000 (tunai) + 20.000 (masuk) - 5.000 (keluar) = 165.000
        $this->assertSame(165000.0, (float) $shift->expected_cash);
        $this->assertSame(165000.0, (float) $shift->closing_cash);
        $this->assertSame(0.0, (float) $shift->cash_difference);
        $this->assertTrue((bool) $shift->is_auto_closed);
    }

    public function test_physical_cash_count_setting_enables_physical_form_and_difference_calculation(): void
    {
        CafeSetting::set('require_physical_cash_count', '1');

        $kasir = User::factory()->kasir()->create();
        $this->actingAs($kasir);

        $this->post('/kasir/shift/buka', []);
        $shift = CashShift::where('user_id', $kasir->id)->firstOrFail();

        // Halaman tutup shift menampilkan opsi hitung fisik
        $this->get('/kasir/shift/tutup')
            ->assertOk()
            ->assertSee('Mode hitung fisik aktif')
            ->assertSee('Jumlah Kas Fisik di Laci');

        // Gagal jika closing_cash tidak diisi
        $this->post('/kasir/shift/tutup', [])
            ->assertSessionHasErrors('closing_cash');

        // Berhasil saat closing_cash diisi, dan selisih terhitung
        $this->post('/kasir/shift/tutup', ['closing_cash' => 95000])
            ->assertRedirect(route('kasir.shift.receipt', $shift));

        $shift->refresh();
        $this->assertSame(100000.0, (float) $shift->expected_cash);
        $this->assertSame(95000.0, (float) $shift->closing_cash);
        $this->assertSame(-5000.0, (float) $shift->cash_difference);
        $this->assertFalse((bool) $shift->is_auto_closed);
    }

    public function test_cash_adjustment_can_only_be_made_by_owner_or_admin_and_affects_next_shift(): void
    {
        $kasir = User::factory()->kasir()->create();
        $owner = User::factory()->owner()->create();

        // Kasir ditolak saat mencoba membuat penyesuaian kas
        $this->actingAs($kasir);
        $this->post('/owner/penyesuaian-kas', [
            'amount' => 15000,
            'reason' => 'Selisih fisik',
        ])->assertForbidden();

        // Owner dapat membuat penyesuaian kas
        $this->actingAs($owner);
        $this->from(route('owner.cash_adjustments.index'))
            ->post('/owner/penyesuaian-kas', [
                'amount' => 15000,
                'reason' => 'Kelebihan kas fisik ditemukan di laci',
            ])->assertRedirect(route('owner.cash_adjustments.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('kas', [
            'amount' => 15000,
            'reason' => 'Kelebihan kas fisik ditemukan di laci',
            'shift_id' => null,
        ]);

        // Tercatat di log aktivitas
        $this->assertDatabaseHas('log_aktivitas', [
            'activity_type' => 'sistem',
        ]);

        // Kasir membuka shift baru, kas awal terhitung: 100.000 + 15.000 = 115.000
        $this->actingAs($kasir);
        $this->post('/kasir/shift/buka', []);

        $shift = CashShift::where('user_id', $kasir->id)->firstOrFail();
        $this->assertSame(115000.0, (float) $shift->opening_cash);

        // Penyesuaian kas sekarang tertaut ke shift ini
        $adjustment = CashAdjustment::firstOrFail();
        $this->assertSame($shift->id, $adjustment->applied_to_shift_id);
    }

    public function test_cannot_close_shift_with_unpaid_or_active_orders(): void
    {
        $kasir = User::factory()->kasir()->create();
        $this->actingAs($kasir);

        $this->post('/kasir/shift/buka', []);
        $shift = CashShift::where('user_id', $kasir->id)->firstOrFail();

        Order::factory()->create([
            'cash_shift_id' => $shift->id,
            'payment_status' => 'menunggu_pembayaran',
            'order_status' => 'diproses',
        ]);

        $this->post('/kasir/shift/tutup', [])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('buka', $shift->fresh()->status);
    }

    public function test_prevent_two_active_shifts_simultaneously_on_shared_drawer(): void
    {
        $kasirA = User::factory()->kasir()->create();
        $kasirB = User::factory()->kasir()->create();

        $this->actingAs($kasirA);
        $this->post('/kasir/shift/buka', []);

        // Kasir B mencoba membuka shift saat shift Kasir A masih buka
        $this->actingAs($kasirB);
        $this->post('/kasir/shift/buka', [])
            ->assertSessionHasErrors('shift');

        $this->assertSame(1, CashShift::count());
    }
}
