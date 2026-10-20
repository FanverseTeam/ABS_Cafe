<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\Concerns\RequiresLegacyAdmin;
use Tests\TestCase;

#[Group('legacy')]
class MenuTest extends TestCase
{
    use RefreshDatabase;
    use RequiresLegacyAdmin;

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

    public function test_legacy_admin_can_create_menu_with_variants(): void
    {
        $category = Category::factory()->create();

        $this->actingAsAdmin()->post('/admin/menu/store', [
            'name' => 'Teh Tarik (Hot / Ice)',
            'category_id' => $category->id,
            'price' => 8000,
            'variants_present' => 1,
            'variants' => [
                ['name' => 'Ice', 'price' => 9000, 'is_available' => '1'],
                ['name' => 'Hot', 'price' => 8000, 'is_available' => '1'],
            ],
        ])->assertRedirect();

        $menu = Menu::where('name', 'Teh Tarik (Hot / Ice)')->firstOrFail();

        $this->assertSame(8000.0, (float) $menu->price);
        $this->assertSame(2, $menu->variants()->count());
    }

    public function test_legacy_admin_can_merge_menus(): void
    {
        $category = Category::factory()->create();
        $ice = Menu::factory()->for($category)->create(['name' => 'Teh Tarik (Ice)']);
        $hot = Menu::factory()->for($category)->create(['name' => 'Teh Tarik (Hot)']);

        $this->actingAsAdmin()->post('/admin/menu/gabungkan', [
            'menu_ids' => [$ice->id, $hot->id],
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('menus', ['name' => 'Teh Tarik (Hot / Ice)', 'deleted_at' => null]);
    }
}
