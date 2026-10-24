<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Support\Emoji;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class EmojiFreeContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_source_seed_and_sql_files_do_not_contain_emoji(): void
    {
        $paths = [
            resource_path(),
            base_path('app'),
            base_path('config'),
            database_path('seeders'),
            database_path('factories'),
            base_path('db_abscafe.sql'),
            base_path('README.md'),
        ];

        $files = [];

        foreach ($paths as $path) {
            if (is_file($path)) {
                $files[] = $path;

                continue;
            }

            if (! is_dir($path)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[] = $file->getPathname();
                }
            }
        }

        $offending = [];

        foreach (array_unique($files) as $file) {
            if (! $this->isTextFile($file)) {
                continue;
            }

            $contents = file_get_contents($file);

            if ($contents !== false && Emoji::contains($contents)) {
                $offending[] = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file);
            }
        }

        $this->assertSame([], $offending, 'Emoji ditemukan di: '.implode(', ', $offending));
    }

    public function test_main_pages_for_every_role_do_not_contain_emoji(): void
    {
        $this->assertNoEmoji($this->get('/login')->assertOk()->getContent(), '/login');
        $this->assertNoEmoji($this->get('/staf')->assertOk()->getContent(), '/staf');

        $this->assertNoEmoji(
            $this->actingAsAdmin()->get(route('admin.dashboard'))->assertOk()->getContent(),
            'admin.dashboard'
        );

        $this->assertNoEmoji(
            $this->actingAsOwner()->get(route('owner.dashboard'))->assertOk()->getContent(),
            'owner.dashboard'
        );

        $this->assertNoEmoji(
            $this->actingAsKasir()->get(route('kasir.dashboard'))->assertOk()->getContent(),
            'kasir.dashboard'
        );
    }

    public function test_admin_input_containing_emoji_is_rejected(): void
    {
        $this->actingAsAdmin();

        $category = Category::factory()->create();

        $response = $this->from(route('admin.catalog.index'))
            ->post(route('admin.catalog.menu.store'), [
                'name' => "Kopi Senja \u{2615}",
                'category_id' => $category->id,
                'price' => 25000,
                'description' => "Nikmat \u{1F60B}",
                'stock' => 10,
                'min_stock' => 3,
                'unit' => 'cup',
            ]);

        $response->assertRedirect(route('admin.catalog.index'))
            ->assertSessionHasErrors(['name']);

        $this->assertSame('Emoji tidak diperbolehkan.', session('errors')->first('name'));
        $this->assertDatabaseMissing('menu', ['name' => "Kopi Senja \u{2615}"]);
    }

    public function test_cleanup_migration_removes_emoji_from_stored_text(): void
    {
        $menu = Menu::factory()->create([
            'name' => "Kopi Senja \u{2615}",
            'description' => "Nikmat \u{1F60B}",
            'unit' => 'cup',
        ]);

        $migration = require database_path('migrations/2026_10_20_083704_strip_emoji_from_stored_text.php');
        $ref = new \ReflectionProperty($migration, 'columns');
        $cols = $ref->getValue($migration);
        $cols['menu'] = ['name', 'description', 'unit'];
        $ref->setValue($migration, $cols);
        $migration->up();

        $menu->refresh();

        $this->assertSame('Kopi Senja', $menu->name);
        $this->assertSame('Nikmat', $menu->description);
    }

    private function assertNoEmoji(string $content, string $page): void
    {
        preg_match_all(Emoji::PATTERN, $content, $matches);

        $this->assertSame([], $matches[0], "Emoji ditemukan pada {$page}: ".implode(' ', $matches[0]));
    }

    private function isTextFile(string $path): bool
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, [
            'php',
            'blade',
            'js',
            'mjs',
            'css',
            'json',
            'md',
            'sql',
            'txt',
            'xml',
            'yml',
            'yaml',
        ], true);
    }
}
