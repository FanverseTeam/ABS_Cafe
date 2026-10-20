<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariantMenuAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_menu_with_variants(): void
    {
        $category = Category::factory()->create();

        $this->actingAsAdmin()->post('/admin/katalog/menu/store', [
            'name' => 'Vietnam Drip (Hot / Ice)',
            'category_id' => $category->id,
            'price' => 13000,
            'stock' => 30,
            'min_stock' => 5,
            'unit' => 'cup',
            'variants_present' => 1,
            'variants' => [
                ['name' => 'Ice', 'size' => 'Large', 'price' => 18000, 'is_available' => '1'],
                ['name' => 'Hot', 'size' => null, 'price' => 15000, 'is_available' => '0'],
            ],
        ])->assertRedirect();

        $menu = Menu::where('name', 'Vietnam Drip (Hot / Ice)')->firstOrFail();

        $this->assertSame(15000.0, (float) $menu->price);
        $this->assertSame(2, $menu->variants()->count());
        $this->assertDatabaseHas('menu_variants', [
            'menu_id' => $menu->id,
            'name' => 'Hot',
            'is_available' => false,
        ]);
    }

    public function test_admin_can_update_and_remove_variants(): void
    {
        $menu = Menu::factory()->create(['name' => 'Teh Tarik (Hot / Ice)']);
        $ice = MenuVariant::factory()->for($menu)->create([
            'name' => 'Ice',
            'size' => 'Large',
            'price' => 18000,
        ]);
        MenuVariant::factory()->for($menu)->create([
            'name' => 'Hot',
            'size' => null,
            'price' => 15000,
        ]);

        $this->actingAsAdmin()->post("/admin/katalog/menu/{$menu->id}/update", [
            'name' => 'Teh Tarik (Hot / Ice)',
            'category_id' => $menu->category_id,
            'price' => 15000,
            'variants_present' => 1,
            'variants' => [
                ['id' => $ice->id, 'name' => 'Ice', 'size' => 'Reguler', 'price' => 16000, 'is_available' => '1'],
            ],
        ])->assertRedirect();

        $this->assertSame(1, $menu->variants()->count());
        $this->assertSame(1, $menu->fresh()->variants()->where('name', 'Ice')->where('size', 'Reguler')->count());
    }

    public function test_admin_can_merge_selected_menus(): void
    {
        $category = Category::factory()->create();
        $ice = Menu::factory()->for($category)->create(['name' => 'Cappucino (Ice)', 'price' => 15000]);
        $hot = Menu::factory()->for($category)->create(['name' => 'Cappucino (Hot)', 'price' => 16000]);

        $this->actingAsAdmin()->post('/admin/katalog/menu/gabungkan', [
            'menu_ids' => [$ice->id, $hot->id],
        ])->assertRedirect()->assertSessionHas('success');

        $parent = Menu::where('name', 'Cappucino (Hot / Ice)')->firstOrFail();

        $this->assertSame(2, $parent->variants()->count());
        $this->assertSoftDeleted('menus', ['id' => $ice->id]);
        $this->assertSoftDeleted('menus', ['id' => $hot->id]);
    }

    public function test_menu_list_shows_variant_chips_and_price_range(): void
    {
        $menu = Menu::factory()->create(['name' => 'Matcha Milk (Hot / Ice)', 'price' => 15000]);
        MenuVariant::factory()->for($menu)->create(['name' => 'Ice', 'price' => 17000]);
        MenuVariant::factory()->for($menu)->create(['name' => 'Hot', 'price' => 15000]);

        $this->actingAsAdmin()->get('/admin/katalog')
            ->assertOk()
            ->assertSee('Matcha Milk (Hot / Ice)')
            ->assertSee('Ice')
            ->assertSee('Hot')
            ->assertSee('Rp 15.000 - Rp 17.000', false);
    }
}
