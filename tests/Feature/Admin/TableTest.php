<?php

namespace Tests\Feature\Admin;

use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_index_loads(): void
    {
        Table::factory()->create(['number' => 88, 'status' => 'tersedia']);

        $this->actingAsAdmin()->get('/admin/meja')->assertOk()->assertSee('88');
    }

    public function test_admin_can_create_table(): void
    {
        $this->actingAsAdmin()->post('/admin/meja/store', [
            'number' => 12,
            'capacity' => 4,
            'status' => 'tersedia',
            'location_notes' => 'Lantai 2',
        ])->assertRedirect();

        $this->assertDatabaseHas('tables', ['number' => 12, 'capacity' => 4]);
    }

    public function test_table_number_must_be_unique(): void
    {
        Table::factory()->create(['number' => 12]);

        $this->actingAsAdmin()->post('/admin/meja/store', [
            'number' => 12,
            'capacity' => 4,
            'status' => 'tersedia',
        ])->assertSessionHasErrors('number');
    }

    public function test_create_table_validates_status(): void
    {
        $this->actingAsAdmin()->post('/admin/meja/store', [
            'number' => 30,
            'capacity' => 4,
            'status' => 'ngawur',
        ])->assertSessionHasErrors('status');
    }

    public function test_admin_can_update_table(): void
    {
        $table = Table::factory()->create(['number' => 5, 'capacity' => 2]);

        $this->actingAsAdmin()->post("/admin/meja/{$table->id}/update", [
            'number' => 5,
            'capacity' => 8,
            'status' => 'terpakai',
        ])->assertRedirect();

        $table->refresh();
        $this->assertSame(8, $table->capacity);
        $this->assertSame('terpakai', $table->status);
    }

    public function test_admin_can_update_table_status(): void
    {
        $table = Table::factory()->create(['status' => 'tersedia']);

        $this->actingAsAdmin()->post("/admin/meja/{$table->id}/status", ['status' => 'nonaktif'])
            ->assertRedirect();

        $this->assertSame('nonaktif', $table->fresh()->status);
    }

    public function test_update_table_status_validates_value(): void
    {
        $table = Table::factory()->create();

        $this->actingAsAdmin()->post("/admin/meja/{$table->id}/status", ['status' => 'rusak'])
            ->assertSessionHasErrors('status');
    }

    public function test_admin_can_delete_table(): void
    {
        $table = Table::factory()->create();

        $this->actingAsAdmin()->delete("/admin/meja/{$table->id}")->assertRedirect();

        $this->assertDatabaseMissing('tables', ['id' => $table->id]);
    }
}
