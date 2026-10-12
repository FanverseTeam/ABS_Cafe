<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_index_loads(): void
    {
        $category = Category::factory()->create();
        Menu::factory()->for($category)->create(['name' => 'Es Kopi Susu']);

        $this->actingAsAdmin()->get('/admin/menu')->assertOk()->assertSee('Es Kopi Susu');
    }

    public function test_admin_can_create_menu(): void
    {
        $category = Category::factory()->create();

        $this->actingAsAdmin()->post('/admin/menu/store', [
            'name' => 'Matcha Baru',
            'category_id' => $category->id,
            'price' => 25000,
            'stock' => 20,
            'min_stock' => 5,
            'unit' => 'cup',
        ])->assertRedirect();

        $this->assertDatabaseHas('menus', ['name' => 'Matcha Baru', 'category_id' => $category->id]);
        $this->assertDatabaseHas('activity_logs', ['activity_type' => 'menu']);
    }

    public function test_create_menu_requires_valid_data(): void
    {
        $this->actingAsAdmin()->post('/admin/menu/store', [])
            ->assertSessionHasErrors(['name', 'category_id', 'price']);
    }

    public function test_admin_can_update_menu(): void
    {
        $menu = Menu::factory()->create(['name' => 'Lama']);
        $category = Category::factory()->create();

        $this->actingAsAdmin()->post("/admin/menu/{$menu->id}/update", [
            'name' => 'Baru',
            'category_id' => $category->id,
            'price' => 30000,
        ])->assertRedirect();

        $this->assertSame('Baru', $menu->fresh()->name);
        $this->assertSame(30000.0, (float) $menu->fresh()->price);
    }

    public function test_admin_can_duplicate_menu(): void
    {
        $menu = Menu::factory()->create(['name' => 'Original', 'sold_count' => 10]);

        $this->actingAsAdmin()->post("/admin/menu/{$menu->id}/duplicate")->assertRedirect();

        $this->assertDatabaseHas('menus', ['name' => 'Original (Salinan)', 'sold_count' => 0]);
    }

    public function test_admin_can_toggle_availability(): void
    {
        $menu = Menu::factory()->create(['is_available' => true]);

        $this->actingAsAdmin()->post("/admin/menu/{$menu->id}/toggle-available")->assertRedirect();

        $this->assertFalse($menu->fresh()->is_available);
    }

    public function test_admin_can_toggle_best_seller(): void
    {
        $menu = Menu::factory()->create(['is_best_seller' => false]);

        $this->actingAsAdmin()->post("/admin/menu/{$menu->id}/toggle-bestseller")->assertRedirect();

        $this->assertTrue($menu->fresh()->is_best_seller);
    }

    public function test_admin_can_delete_menu(): void
    {
        $menu = Menu::factory()->create();

        $this->actingAsAdmin()->delete("/admin/menu/{$menu->id}")->assertRedirect();

        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }
}
