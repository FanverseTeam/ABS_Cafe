<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariantMenuAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_menu_with_variant_group(): void
    {
        $category = Category::factory()->create();

        $this->actingAsAdmin()->post('/admin/katalog/menu/store', [
            'name' => 'Vietnam Drip (Ice)',
            'variant_group' => 'Vietnam Drip',
            'variant_label' => 'Ice',
            'category_id' => $category->id,
            'price' => 15000,
        ])->assertRedirect();

        $menu = Menu::where('name', 'Vietnam Drip (Ice)')->firstOrFail();

        $this->assertSame(15000.0, (float) $menu->price);
        $this->assertSame('Vietnam Drip', $menu->variant_group);
        $this->assertSame('Ice', $menu->variant_label);
    }

    public function test_admin_can_update_variant_group(): void
    {
        $menu = Menu::factory()->create([
            'name' => 'Teh Tarik (Ice)',
            'variant_group' => 'Teh Tarik',
            'variant_label' => 'Ice',
            'price' => 15000,
        ]);

        $this->actingAsAdmin()->post("/admin/katalog/menu/{$menu->id}/update", [
            'name' => 'Teh Tarik (Ice)',
            'variant_group' => 'Teh Tarik Manis',
            'variant_label' => 'Dingin',
            'category_id' => $menu->category_id,
            'price' => 16000,
        ])->assertRedirect();

        $fresh = $menu->fresh();
        $this->assertSame('Teh Tarik Manis', $fresh->variant_group);
        $this->assertSame('Dingin', $fresh->variant_label);
        $this->assertSame(16000.0, (float) $fresh->price);
    }

    public function test_admin_can_merge_selected_menus(): void
    {
        $category = Category::factory()->create();
        $ice = Menu::factory()->for($category)->create(['name' => 'Cappucino (Ice)', 'price' => 15000]);
        $hot = Menu::factory()->for($category)->create(['name' => 'Cappucino (Hot)', 'price' => 16000]);

        $this->actingAsAdmin()->post('/admin/katalog/menu/gabungkan', [
            'menu_ids' => [$ice->id, $hot->id],
        ])->assertRedirect()->assertSessionHas('success');

        $iceFresh = $ice->fresh();
        $hotFresh = $hot->fresh();

        $this->assertSame('Cappucino', $iceFresh->variant_group);
        $this->assertSame('Ice', $iceFresh->variant_label);
        $this->assertSame('Cappucino', $hotFresh->variant_group);
        $this->assertSame('Hot', $hotFresh->variant_label);
    }

    public function test_menu_list_shows_variant_chips(): void
    {
        $category = Category::factory()->create();
        Menu::factory()->for($category)->create([
            'name' => 'Matcha Milk (Ice)',
            'variant_group' => 'Matcha Milk',
            'variant_label' => 'Ice',
            'price' => 17000,
        ]);
        Menu::factory()->for($category)->create([
            'name' => 'Matcha Milk (Hot)',
            'variant_group' => 'Matcha Milk',
            'variant_label' => 'Hot',
            'price' => 15000,
        ]);

        $this->actingAsAdmin()->get('/admin/katalog')
            ->assertOk()
            ->assertSee('Matcha Milk (Ice)')
            ->assertSee('Varian: Ice')
            ->assertSee('Matcha Milk (Hot)')
            ->assertSee('Varian: Hot');
    }
}
