<?php

namespace Tests\Feature\Admin;

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_index_loads(): void
    {
        Menu::factory()->lowStock()->create(['name' => 'Kopi Menipis']);

        $this->actingAsAdmin()->get('/admin/stok')->assertOk()->assertSee('Kopi Menipis');
    }

    public function test_stock_in_increases_stock(): void
    {
        $menu = Menu::factory()->create(['stock' => 10, 'is_available' => false]);

        $this->actingAsAdmin()->post("/admin/stok/{$menu->id}/adjust", [
            'type' => 'masuk',
            'quantity' => 15,
        ])->assertRedirect();

        $menu->refresh();
        $this->assertSame(25, $menu->stock);
        $this->assertTrue($menu->is_available);
        $this->assertDatabaseHas('stock_logs', ['menu_id' => $menu->id, 'type' => 'masuk', 'stock_after' => 25]);
    }

    public function test_stock_out_decreases_stock_and_never_below_zero(): void
    {
        $menu = Menu::factory()->create(['stock' => 5]);

        $this->actingAsAdmin()->post("/admin/stok/{$menu->id}/adjust", [
            'type' => 'keluar',
            'quantity' => 100,
        ])->assertRedirect();

        $menu->refresh();
        $this->assertSame(0, $menu->stock);
        $this->assertFalse($menu->is_available);
    }

    public function test_stock_correction_sets_absolute_value(): void
    {
        $menu = Menu::factory()->create(['stock' => 50]);

        $this->actingAsAdmin()->post("/admin/stok/{$menu->id}/adjust", [
            'type' => 'koreksi',
            'quantity' => 12,
        ])->assertRedirect();

        $this->assertSame(12, $menu->fresh()->stock);
    }

    public function test_adjust_validates_type(): void
    {
        $menu = Menu::factory()->create();

        $this->actingAsAdmin()->post("/admin/stok/{$menu->id}/adjust", [
            'type' => 'salah',
            'quantity' => 1,
        ])->assertSessionHasErrors('type');
    }

    public function test_admin_can_set_min_stock(): void
    {
        $menu = Menu::factory()->create(['min_stock' => 10]);

        $this->actingAsAdmin()->post("/admin/stok/{$menu->id}/min-stock", [
            'min_stock' => 25,
        ])->assertRedirect();

        $this->assertSame(25, $menu->fresh()->min_stock);
    }

    public function test_min_stock_must_be_positive(): void
    {
        $menu = Menu::factory()->create();

        $this->actingAsAdmin()->post("/admin/stok/{$menu->id}/min-stock", ['min_stock' => 0])
            ->assertSessionHasErrors('min_stock');
    }
}
