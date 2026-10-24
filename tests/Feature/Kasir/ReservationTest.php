<?php

namespace Tests\Feature\Kasir;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Budi Reservasi',
            'phone' => '081200000000',
            'party_size' => 2,
            'reservation_date' => today()->toDateString(),
            'reservation_time' => '19:00',
            'notes' => 'dekat jendela',
        ], $overrides);
    }

    public function test_reservation_index_loads(): void
    {
        $this->actingAsKasir()->get('/kasir/reservasi')->assertOk();
    }

    public function test_kasir_can_create_reservation(): void
    {
        $this->actingAsKasir()->post('/kasir/reservasi/store', $this->payload())
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservasi', [
            'customer_name' => 'Budi Reservasi',
            'status' => 'menunggu',
        ]);
    }

    public function test_reservation_requires_customer_and_party_size(): void
    {
        $this->actingAsKasir()->post('/kasir/reservasi/store', [])
            ->assertSessionHasErrors(['customer_name', 'party_size', 'reservation_date', 'reservation_time']);
    }

    public function test_table_must_have_capacity_for_party_size(): void
    {
        $table = Table::factory()->create(['capacity' => 2]);

        $this->actingAsKasir()->post('/kasir/reservasi/store', $this->payload([
            'party_size' => 5,
            'table_id' => $table->id,
        ]))->assertSessionHasErrors('table_id');

        $this->assertDatabaseCount('reservasi', 0);
    }

    public function test_table_time_conflict_is_rejected(): void
    {
        $table = Table::factory()->create(['capacity' => 4]);
        $this->actingAsKasir();

        Reservation::factory()->create([
            'table_id' => $table->id,
            'reservation_date' => today()->toDateString(),
            'reservation_time' => '19:00',
            'status' => 'menunggu',
        ]);

        $this->post('/kasir/reservasi/store', $this->payload([
            'reservation_time' => '19:30',
            'table_id' => $table->id,
        ]))->assertSessionHasErrors('table_id');
    }

    public function test_reservation_with_non_conflicting_time_is_allowed(): void
    {
        $table = Table::factory()->create(['capacity' => 4]);
        $this->actingAsKasir();

        Reservation::factory()->create([
            'table_id' => $table->id,
            'reservation_date' => today()->toDateString(),
            'reservation_time' => '12:00',
            'status' => 'menunggu',
        ]);

        $this->post('/kasir/reservasi/store', $this->payload([
            'reservation_time' => '19:00',
            'table_id' => $table->id,
        ]))->assertRedirect()->assertSessionHas('success');
    }

    public function test_kasir_can_update_reservation(): void
    {
        $this->actingAsKasir();
        $reservation = Reservation::factory()->create();

        $this->post("/kasir/reservasi/{$reservation->id}/update", $this->payload([
            'customer_name' => 'Nama Baru',
        ]))->assertRedirect();

        $this->assertSame('Nama Baru', $reservation->fresh()->customer_name);
    }

    public function test_marking_reservation_datang_occupies_table(): void
    {
        $this->actingAsKasir();
        $table = Table::factory()->create(['number' => 3, 'status' => 'tersedia']);
        $reservation = Reservation::factory()->create(['table_id' => $table->id, 'status' => 'dikonfirmasi']);

        $this->post("/kasir/reservasi/{$reservation->id}/status", ['status' => 'datang'])->assertRedirect();

        $this->assertSame('terpakai', $table->fresh()->status);
        $this->assertSame('datang', $reservation->fresh()->status);
    }

    public function test_marking_reservation_selesai_frees_table_when_idle(): void
    {
        $this->actingAsKasir();
        $table = Table::factory()->create(['number' => 3, 'status' => 'terpakai']);
        $reservation = Reservation::factory()->create(['table_id' => $table->id, 'status' => 'datang']);

        $this->post("/kasir/reservasi/{$reservation->id}/status", ['status' => 'selesai'])->assertRedirect();

        $this->assertSame('tersedia', $table->fresh()->status);
    }

    public function test_status_validation_allows_known_states_only(): void
    {
        $this->actingAsKasir();
        $reservation = Reservation::factory()->create();

        $this->post("/kasir/reservasi/{$reservation->id}/status", ['status' => 'ngawur'])
            ->assertSessionHasErrors('status');
    }

    public function test_kasir_can_delete_reservation(): void
    {
        $this->actingAsKasir();
        $reservation = Reservation::factory()->create();

        $this->delete("/kasir/reservasi/{$reservation->id}")->assertRedirect();

        $this->assertDatabaseMissing('reservasi', ['id' => $reservation->id]);
    }
}
