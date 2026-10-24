<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\Concerns\RequiresLegacyAdmin;
use Tests\TestCase;

#[Group('legacy')]
class CategoryTest extends TestCase
{
    use RefreshDatabase;
    use RequiresLegacyAdmin;

    public function test_category_index_loads(): void
    {
        Category::factory()->create(['name' => 'Minuman']);

        $this->actingAsAdmin()->get('/admin/kategori')->assertOk()->assertSee('Minuman');
    }

    public function test_admin_can_create_category(): void
    {
        $this->actingAsAdmin()->post('/admin/kategori/store', [
            'name' => 'Dessert',
            'icon' => 'cake',
            'description' => 'Manis-manis',
        ])->assertRedirect();

        $this->assertDatabaseHas('kategori', ['name' => 'Dessert']);
    }

    public function test_create_category_requires_name(): void
    {
        $this->actingAsAdmin()->post('/admin/kategori/store', [])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Lama']);

        $this->actingAsAdmin()->post("/admin/kategori/{$category->id}/update", [
            'name' => 'Baru',
        ])->assertRedirect();

        $this->assertSame('Baru', $category->fresh()->name);
    }

    public function test_category_with_menus_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Menu::factory()->for($category)->create();

        $this->actingAsAdmin()->delete("/admin/kategori/{$category->id}")
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('kategori', ['id' => $category->id]);
    }

    public function test_empty_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $this->actingAsAdmin()->delete("/admin/kategori/{$category->id}")
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('kategori', ['id' => $category->id]);
    }
}
