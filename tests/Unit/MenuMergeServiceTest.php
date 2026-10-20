<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promo;
use App\Models\Recipe;
use App\Models\Stock;
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

    public function test_merge_moves_related_data_and_rollback_restores_archived_menus(): void
    {
        $category = Category::factory()->create();
        $ice = Menu::factory()->for($category)->create([
            'name' => 'Vietnam Drip (Ice)',
            'price' => 13000,
            'stock' => 10,
            'sold_count' => 2,
        ]);
        $hot = Menu::factory()->for($category)->create([
            'name' => 'Vietnam Drip (Hot)',
            'price' => 12000,
            'stock' => 5,
            'sold_count' => 1,
        ]);

        $stock = Stock::create([
            'name' => 'Biji Kopi',
            'unit' => 'gram',
            'quantity' => 1000,
            'min_quantity' => 100,
            'is_active' => true,
        ]);
        Recipe::create([
            'menu_id' => $ice->id,
            'menu_variant_id' => null,
            'stock_id' => $stock->id,
            'amount' => 18,
        ]);

        $order = Order::factory()->create();
        $item = OrderItem::factory()->for($order)->create([
            'menu_id' => $ice->id,
            'variant_id' => null,
            'name' => 'Vietnam Drip (Ice)',
            'price' => 13000,
            'quantity' => 2,
            'subtotal' => 26000,
        ]);

        $promo = Promo::factory()->create([
            'applies_to' => 'menu',
            'target_menu_id' => $ice->id,
        ]);

        $user = User::factory()->create();
        Favorite::create(['user_id' => $user->id, 'menu_id' => $ice->id]);

        $service = app(MenuMergeService::class);
        $parent = $service->mergeMenus([$ice->id, $hot->id]);

        $this->assertSame('Vietnam Drip (Hot / Ice)', $parent->name);
        $this->assertSame(2, $parent->variants()->count());
        $this->assertSame(12000.0, (float) $parent->price);
        $this->assertSame(15, (int) $parent->stock);
        $this->assertSame(3, (int) $parent->sold_count);

        $this->assertSoftDeleted('menus', ['id' => $ice->id]);
        $this->assertSoftDeleted('menus', ['id' => $hot->id]);

        $iceVariant = $parent->variants()->where('name', 'Ice')->firstOrFail();
        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'menu_id' => $parent->id,
            'variant_id' => $iceVariant->id,
            'name' => 'Vietnam Drip - Ice',
        ]);
        $this->assertSame(1, Recipe::where('menu_id', $parent->id)->whereNotNull('menu_variant_id')->count());
        $this->assertDatabaseHas('promos', ['id' => $promo->id, 'target_menu_id' => $parent->id]);
        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'menu_id' => $parent->id]);

        $restored = $service->rollback($parent);

        $this->assertSame(2, $restored);
        $this->assertSoftDeleted('menus', ['id' => $parent->id]);
        $this->assertDatabaseHas('menus', ['id' => $ice->id, 'merged_into_menu_id' => null, 'deleted_at' => null]);
        $this->assertDatabaseHas('menus', ['id' => $hot->id, 'merged_into_menu_id' => null, 'deleted_at' => null]);
        $this->assertDatabaseHas('order_items', ['id' => $item->id, 'menu_id' => $ice->id]);
        $this->assertDatabaseHas('promos', ['id' => $promo->id, 'target_menu_id' => $ice->id]);
        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'menu_id' => $ice->id]);
        $this->assertDatabaseMissing('menu_variants', ['menu_id' => $parent->id]);
    }

    public function test_variant_sizes_survive_merge_and_order_items_map_to_the_right_variant(): void
    {
        $category = Category::factory()->create();
        $ice = Menu::factory()->for($category)->create(['name' => 'Teh Tarik (Ice)']);
        $hot = Menu::factory()->for($category)->create(['name' => 'Teh Tarik (Hot)']);

        $iceLarge = MenuVariant::factory()->for($ice)->create([
            'name' => 'Ice',
            'size' => 'Large',
            'price' => 18000,
            'sort_order' => 0,
        ]);
        MenuVariant::factory()->for($ice)->create([
            'name' => 'Ice',
            'size' => 'Reguler',
            'price' => 14000,
            'sort_order' => 1,
        ]);
        MenuVariant::factory()->for($hot)->create([
            'name' => 'Hot',
            'size' => null,
            'price' => 15000,
            'sort_order' => 0,
        ]);

        $order = Order::factory()->create();
        $item = OrderItem::factory()->for($order)->create([
            'menu_id' => $ice->id,
            'variant_id' => $iceLarge->id,
            'name' => 'Teh Tarik (Ice)',
        ]);

        $parent = app(MenuMergeService::class)->mergeMenus([$ice->id, $hot->id]);

        $newVariant = $parent->variants()->where('name', 'Ice')->where('size', 'Large')->firstOrFail();

        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'menu_id' => $parent->id,
            'variant_id' => $newVariant->id,
            'name' => 'Teh Tarik - Ice - Large',
        ]);
    }
}
