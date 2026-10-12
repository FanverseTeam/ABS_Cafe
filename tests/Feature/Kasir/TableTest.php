<?php

namespace Tests\Feature\Kasir;

use App\Models\Order;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableTest extends TestCase
{
    use RefreshDatabase;

    public function test_kasir_can_update_table_status(): void
    {
        $this->actingAsKasir();
        $table = Table::factory()->create(['status' => 'tersedia']);

        $this->post("/kasir/meja/{$table->id}/status", ['status' => 'terpakai'])->assertRedirect();

        $this->assertSame('terpakai', $table->fresh()->status);
    }

    public function test_occupied_table_cannot_be_deactivated(): void
    {
        $this->actingAsKasir();
        $table = Table::factory()->create(['number' => 4, 'status' => 'terpakai']);
        Order::factory()->create(['table_number' => 4, 'payment_status' => 'menunggu_pembayaran', 'order_status' => 'diproses']);

        $this->post("/kasir/meja/{$table->id}/status", ['status' => 'nonaktif'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('terpakai', $table->fresh()->status);
    }

    public function test_status_validation(): void
    {
        $this->actingAsKasir();
        $table = Table::factory()->create();

        $this->post("/kasir/meja/{$table->id}/status", ['status' => 'rusak'])
            ->assertSessionHasErrors('status');
    }
}