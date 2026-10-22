<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogMobileMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_catalog_renders_exactly_one_card_per_menu(): void
    {
        $user = User::factory()->admin()->create();
        $cat = Category::create(['name' => 'Kopi Pilihan', 'slug' => 'kopi-pilihan']);

        $menus = Menu::factory()->count(4)->create([
            'category_id' => $cat->id,
            'is_available' => true,
        ]);

        $this->actingAs($user);
        $response = $this->get(route('admin.catalog.index'));

        $response->assertOk();
        $content = $response->getContent();

        // Each menu rendered exactly once with data-menu-id
        foreach ($menus as $menu) {
            $occurrences = substr_count($content, 'data-menu-id="'.$menu->id.'"');
            $this->assertSame(1, $occurrences, "Menu ID {$menu->id} should appear exactly once.");
        }

        // Single-mode grid: 2-col mobile, 3-col tablet (no viewMode/localStorage toggle)
        $this->assertStringContainsString('grid grid-cols-2 md:grid-cols-3', $content);
        $this->assertStringNotContainsString('abscafe_menu_view', $content);
        $this->assertStringNotContainsString('viewMode', $content);

        // No list/grid toggle buttons
        $this->assertStringNotContainsString('Tampilan Daftar', $content);
        $this->assertStringNotContainsString('Tampilan Kotak', $content);

        // Square photo: aspect-square + object-cover
        $this->assertStringContainsString('aspect-square', $content);
        $this->assertStringContainsString('object-cover', $content);

        // Card anatomy: name clamp, category olive, price tabular
        $this->assertStringContainsString('line-clamp-2', $content);
        $this->assertStringContainsString('tabular-nums', $content);
        $this->assertStringContainsString('whitespace-nowrap', $content);

        // Footer toggle: min 44px touch target
        $this->assertStringContainsString('min-w-[44px] min-h-[44px]', $content);

        // Bottom clearance for FAB (>= 112px)
        $this->assertStringContainsString('h-32 sm:h-36', $content);

        // Topbar safe-area & FAB positioning
        $this->assertStringContainsString('env(safe-area-inset-top', $content);
        $this->assertStringContainsString('calc(64px + env(safe-area-inset-bottom, 0px) + 16px)', $content);
    }

    public function test_mobile_catalog_shows_best_seller_ribbon_and_habis_badge(): void
    {
        $user = User::factory()->admin()->create();
        $cat = Category::create(['name' => 'Minuman', 'slug' => 'minuman']);

        Menu::factory()->create([
            'name' => 'Kopi Best',
            'category_id' => $cat->id,
            'is_best_seller' => true,
            'is_available' => true,
        ]);

        Menu::factory()->create([
            'name' => 'Es Habis',
            'category_id' => $cat->id,
            'is_best_seller' => false,
            'is_available' => false,
        ]);

        $this->actingAs($user);
        $content = $this->get(route('admin.catalog.index'))->getContent();

        // Best Seller ribbon present for best seller menu
        $this->assertStringContainsString('Best Seller', $content);

        // Habis badge (top-right on photo) present for unavailable menu
        $this->assertMatchesRegularExpression('/top-1\.5 right-1\.5.*?Habis/s', $content);
    }
}
