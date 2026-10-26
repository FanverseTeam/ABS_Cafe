<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreadcrumbResponsiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_breadcrumb_is_hidden_below_lg_and_visible_from_lg_on_admin_pages(): void
    {
        $this->actingAsAdmin();

        $pages = [
            route('admin.staff.index') => 'Pengguna (Staf/Kasir)',
            route('admin.catalog.index') => 'Manajemen Menu',
            route('admin.operations.index') => 'Meja',
            route('admin.vouchers.index') => 'Promo & Diskon',
            route('admin.website.index') => 'Pengaturan Website',
            route('admin.activity.index') => 'Log Aktivitas',
        ];

        foreach ($pages as $url => $label) {
            $content = $this->get($url)->assertOk()->getContent();

            $this->assertSame(
                'cafe-breadcrumb hidden lg:flex',
                $this->breadcrumbClassFrom($content),
                "Breadcrumb pada {$url} tidak memakai class responsif yang diharapkan."
            );

            $this->assertStringContainsString($label, html_entity_decode($this->breadcrumbFrom($content)));
        }
    }

    public function test_breadcrumb_on_create_form_is_hidden_below_lg_and_back_link_remains(): void
    {
        $content = $this->actingAsAdmin()->get(route('admin.staff.create'))->assertOk()->getContent();

        $this->assertSame('cafe-breadcrumb hidden lg:flex', $this->breadcrumbClassFrom($content));
        $this->assertStringContainsString('Tambah Baru', $this->breadcrumbFrom($content));
        $this->assertStringContainsString('title="Kembali ke Daftar Staf"', $content);
    }

    private function breadcrumbFrom(string $content): string
    {
        preg_match('/<nav[^>]*aria-label="Breadcrumb"[^>]*>.*?<\/nav>/s', $content, $matches);

        $this->assertNotEmpty($matches, 'Komponen breadcrumb tidak ditemukan pada respons.');

        return $matches[0];
    }

    private function breadcrumbClassFrom(string $content): string
    {
        preg_match('/<nav[^>]*class="([^"]*cafe-breadcrumb[^"]*)"/', $this->breadcrumbFrom($content), $matches);

        $this->assertNotEmpty($matches, 'Class breadcrumb tidak ditemukan.');

        $classes = preg_split('/\s+/', trim($matches[1]));
        $prefix = array_slice($classes, 0, 3);

        return implode(' ', $prefix);
    }
}
