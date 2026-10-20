<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class TypographyTest extends TestCase
{
    use RefreshDatabase;

    public function test_source_files_do_not_reference_playfair_or_google_fonts(): void
    {
        $paths = [
            resource_path('views'),
            resource_path('css'),
            resource_path('js'),
            public_path('css'),
            public_path('js'),
            config_path(),
            base_path('tailwind.config.js'),
            base_path('vite.config.js'),
        ];

        $offending = [];

        foreach ($paths as $path) {
            if (is_file($path)) {
                $this->scanFile($path, $offending);

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
                    $this->scanFile($file->getPathname(), $offending);
                }
            }
        }

        $this->assertSame([], $offending, 'Referensi font lama ditemukan di: '.implode(', ', $offending));
    }

    public function test_noto_serif_font_files_are_published_and_readable(): void
    {
        $variants = [
            'noto-serif-latin-400-normal.woff2',
            'noto-serif-latin-600-normal.woff2',
            'noto-serif-latin-700-normal.woff2',
            'noto-serif-latin-400-italic.woff2',
            'noto-serif-latin-700-italic.woff2',
        ];

        foreach ($variants as $variant) {
            $path = public_path("fonts/noto-serif/{$variant}");

            $this->assertFileExists($path);
            $this->assertFileIsReadable($path);
            $this->assertGreaterThan(0, filesize($path), "Font {$variant} kosong.");
        }

        $css = file_get_contents(public_path('css/fonts.css'));

        $this->assertStringContainsString("font-family: 'Noto Serif'", $css);
        $this->assertStringContainsString('font-display: swap', $css);

        foreach ($variants as $variant) {
            $this->assertStringContainsString("../fonts/noto-serif/{$variant}", $css);
        }
    }

    public function test_main_pages_use_local_fonts_and_do_not_request_google_fonts(): void
    {
        $pages = [
            '/login' => $this->get('/login')->assertOk(),
            '/staf' => $this->get('/staf')->assertOk(),
            'admin' => $this->actingAsAdmin()->get(route('admin.dashboard'))->assertOk(),
            'owner' => $this->actingAsOwner()->get(route('owner.dashboard'))->assertOk(),
            'kasir' => $this->actingAsKasir()->get(route('kasir.dashboard'))->assertOk(),
        ];

        foreach ($pages as $page => $response) {
            $html = $response->getContent();

            $this->assertStringNotContainsString('fonts.'.'googleapis.com', $html, "Google Fonts masih dimuat di {$page}.");
            $this->assertStringNotContainsString('fonts.'.'gstatic.com', $html, 'Aset Google Fonts masih dimuat.');
            $this->assertStringContainsString('css/fonts.css', $html, "Font lokal belum dimuat di {$page}.");
            $this->assertStringContainsString(
                'fonts/noto-serif/noto-serif-latin-400-normal.woff2',
                $html,
                "Preload Noto Serif belum ada di {$page}."
            );
        }
    }

    public function test_statistical_numbers_use_lining_and_tabular_figures(): void
    {
        $adminCss = file_get_contents(public_path('css/admin-custom.css'));
        $appCss = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('font-variant-numeric: lining-nums tabular-nums;', $adminCss);
        $this->assertStringContainsString('font-variant-numeric: lining-nums tabular-nums;', $appCss);
    }

    /**
     * @param  list<string>  $offending
     */
    private function scanFile(string $path, array &$offending): void
    {
        if (! in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), [
            'php',
            'css',
            'js',
            'json',
            'md',
            'xml',
            'yml',
            'yaml',
        ], true)) {
            return;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return;
        }

        $needles = ['Play'.'fair', 'fonts.'.'googleapis.com', 'fonts.'.'gstatic.com', 'font-'.'playfair'];

        foreach ($needles as $needle) {
            if (stripos($contents, $needle) !== false) {
                $relative = str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
                $offending[] = "{$relative} ({$needle})";

                break;
            }
        }
    }
}
