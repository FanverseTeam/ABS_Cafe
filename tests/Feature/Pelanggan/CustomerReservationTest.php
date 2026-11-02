<?php

namespace Tests\Feature\Pelanggan;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CustomerReservationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_customer_can_access_reservation_page(): void
    {
        Table::factory()->create([
            'number' => 1,
            'capacity' => 4,
            'status' => 'tersedia',
            'area' => 'indoor',
        ]);

        $response = $this->get(route('pelanggan.reservasi'));

        $response->assertOk();
        $response->assertSee('Reservasi Meja');
        $response->assertSee('Kondisi Meja Real-Time');
        $response->assertSee('Meja #1');
        $response->assertSee('Lengkapi Detail Reservasi');
    }

    public function test_realtime_table_status_json_returns_table_availability(): void
    {
        $table = Table::factory()->create([
            'number' => 2,
            'capacity' => 2,
            'status' => 'tersedia',
            'area' => 'outdoor',
        ]);

        $response = $this->getJson(route('pelanggan.reservasi.tables', [
            'date' => today()->toDateString(),
            'time' => '19:00',
        ]));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('summary.total', 1)
            ->assertJsonPath('summary.tersedia', 1);
    }

    public function test_customer_can_submit_reservation_successfully(): void
    {
        $table = Table::factory()->create([
            'number' => 3,
            'capacity' => 4,
            'status' => 'tersedia',
        ]);

        $payload = [
            'customer_name' => 'Ahmad Pelanggan',
            'phone' => '081234567890',
            'party_size' => 3,
            'reservation_date' => today()->toDateString(),
            'reservation_time' => '18:30',
            'table_id' => $table->id,
            'notes' => 'Tolong dekat colokan listrik',
        ];

        $response = $this->post(route('pelanggan.reservasi.store'), $payload);

        $response->assertRedirect(route('pelanggan.reservasi'));
        $response->assertSessionHas('reservation_success', true);

        $this->assertDatabaseHas('reservasi', [
            'customer_name' => 'Ahmad Pelanggan',
            'phone' => '081234567890',
            'party_size' => 3,
            'table_id' => $table->id,
            'status' => 'menunggu',
        ]);
    }

    public function test_customer_cannot_reserve_table_beyond_capacity(): void
    {
        $table = Table::factory()->create([
            'number' => 4,
            'capacity' => 2,
            'status' => 'tersedia',
        ]);

        $payload = [
            'customer_name' => 'Siti Nurhaliza',
            'phone' => '089876543210',
            'party_size' => 6,
            'reservation_date' => today()->toDateString(),
            'reservation_time' => '19:00',
            'table_id' => $table->id,
        ];

        $response = $this->post(route('pelanggan.reservasi.store'), $payload);

        $response->assertSessionHasErrors('table_id');
        $this->assertDatabaseMissing('reservasi', [
            'customer_name' => 'Siti Nurhaliza',
        ]);
    }

    public function test_customer_cannot_reserve_conflicting_table_slot(): void
    {
        $table = Table::factory()->create([
            'number' => 5,
            'capacity' => 4,
            'status' => 'tersedia',
        ]);

        Reservation::factory()->create([
            'table_id' => $table->id,
            'reservation_date' => today()->toDateString(),
            'reservation_time' => '19:00',
            'status' => 'dikonfirmasi',
        ]);

        // Pelanggan lain mencoba memesan di meja yang sama jam 19:30 (kurang dari 2 jam)
        $payload = [
            'customer_name' => 'Doni Pemesan',
            'phone' => '081211112222',
            'party_size' => 2,
            'reservation_date' => today()->toDateString(),
            'reservation_time' => '19:30',
            'table_id' => $table->id,
        ];

        $response = $this->post(route('pelanggan.reservasi.store'), $payload);

        $response->assertSessionHasErrors('table_id');
    }

    public function test_customer_can_check_reservation_status(): void
    {
        $reservation = Reservation::factory()->create([
            'code' => 'RSV-261102-999',
            'customer_name' => 'Rina Cantika',
            'phone' => '085544332211',
            'status' => 'dikonfirmasi',
        ]);

        $response = $this->getJson(route('pelanggan.reservasi.check', ['q' => 'RSV-261102-999']));

        $response->assertOk()
            ->assertJsonPath('found', true)
            ->assertJsonPath('reservation.code', 'RSV-261102-999')
            ->assertJsonPath('reservation.customer_name', 'Rina Cantika')
            ->assertJsonPath('reservation.status', 'dikonfirmasi');
    }
}
