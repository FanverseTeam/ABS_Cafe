<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Menu;
use App\Models\Promo;
use App\Models\User;
use App\Services\MenuMergeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuMergeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_analysis_detects_strict_hot_ice_pairs_only(): void
    {
        $category = Category::factory()->create();

        Menu::factory()->for($category)->create(['name' => 'Vietnam Drip (Ice)']);
        Menu::factory()->for($category)->create(['name' => 'Vietnam Drip (Hot)']);
        Menu::factory()->for($category)->create(['name' => 'Kopi Susu (Ice)']);
        Menu::factory()->for($category)->create(['name' => 'Kopi Susu']);

        $analysis = app(MenuMergeService::class)->analyze();

        $this->assertCount(1, $analysis['groups']);
        $this->assertSame('Vietnam Drip', $analysis['groups'][0]['base']);
        $this->assertSame(['Hot', 'Ice'], $analysis['groups'][0]['variant_labels']);
        $this->assertNotEmpty($analysis['ambiguous']);
    }

    public function test_merge_groups_menus_and_rollback_ungroups(): void
    {
        $category = Category::factory()->create();
        $ice = Menu::factory()->for($category)->create([
            'name' => 'Vietnam Drip (Ice)',
            'price' => 13000,
            'sold_count' => 2,
        ]);
        $hot = Menu::factory()->for($category)->create([
            'name' => 'Vietnam Drip (Hot)',
            'price' => 12000,
            'sold_count' => 1,
        ]);

        $promo = Promo::factory()->create([
            'applies_to' => 'menu',
            'target_menu_id' => $ice->id,
        ]);

        $user = User::factory()->create();
        Favorite::create(['user_id' => $user->id, 'menu_id' => $ice->id]);

        $service = app(MenuMergeService::class);
        $first = $service->mergeMenus([$ice->id, $hot->id]);

        $this->assertSame('Vietnam Drip', $ice->fresh()->variant_group);
        $this->assertSame('Ice', $ice->fresh()->variant_label);
        $this->assertSame('Vietnam Drip', $hot->fresh()->variant_group);
        $this->assertSame('Hot', $hot->fresh()->variant_label);

        // Menus remain active with their own PK
        $this->assertDatabaseHas('menu', ['id' => $ice->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('menu', ['id' => $hot->id, 'deleted_at' => null]);

        $restored = $service->rollback($first);

        $this->assertSame(2, $restored);
        $this->assertNull($ice->fresh()->variant_group);
        $this->assertNull($hot->fresh()->variant_group);
    }
}
