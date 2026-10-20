<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_staf_landing_page_is_accessible_for_guests(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('ABS Cafe')
            ->assertSee('Good Coffee')
            ->assertSee('Good Mood')
            ->assertSee('Portal Staf ABS Cafe')
            ->assertSee(route('login'));
    }

    public function test_staf_landing_page_has_correct_meta_tags(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('ABS Cafe – Good Coffee, Good Mood', false) // title
            ->assertSee('Portal Staf ABS Cafe', false) // description
            ->assertSee('og:title', false)
            ->assertSee('og:description', false)
            ->assertSee('og:image', false);
    }

    public function test_landing_page_uses_local_optimized_background_images(): void
    {
        $response = $this->get('/staf')->assertOk();

        foreach ([768, 1280, 1920] as $width) {
            $response->assertSee("images/landing-bg-{$width}.webp", false);
        }

        $response->assertSee('images/landing-bg-1920.jpg', false)
            ->assertSee('srcset', false)
            ->assertSee('sizes="100vw"', false)
            ->assertSee('rel="preload"', false)
            ->assertSee('as="image"', false)
            ->assertSee('decoding="async"', false)
            ->assertSee('object-cover', false)
            ->assertDontSee('LANDING_BG_URL')
            ->assertDontSee('landing_bg_url');

        $html = $response->getContent();

        $this->assertStringContainsString('background-size: cover', $html);
        $this->assertStringContainsString('background-position: center', $html);
    }

    public function test_landing_page_falls_back_to_gradient_when_optimized_images_are_missing(): void
    {
        $files = [
            'landing-bg-1920.webp',
            'landing-bg-1280.webp',
            'landing-bg-768.webp',
            'landing-bg-1920.jpg',
        ];

        $backups = [];

        try {
            foreach ($files as $file) {
                $path = public_path('images/'.$file);

                if (is_file($path)) {
                    $backup = $path.'.test-backup';
                    File::move($path, $backup);
                    $backups[$path] = $backup;
                }
            }

            $response = $this->get('/staf')->assertOk()->assertSee('landing-fallback');

            $html = $response->getContent();

            $this->assertStringNotContainsString('images/landing-bg-', $html);
            $this->assertStringNotContainsString('srcset', $html);
        } finally {
            foreach ($backups as $path => $backup) {
                if (is_file($backup)) {
                    File::move($backup, $path);
                }
            }
        }
    }

    public function test_guest_sees_exactly_one_login_link(): void
    {
        $response = $this->get('/staf')
            ->assertOk()
            ->assertSee('Login');

        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, 'href="'.route('login').'"'));
        $this->assertSame(1, substr_count($html, '>Login<'));
    }

    public function test_admin_sees_one_dashboard_button_linking_to_staff_management(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/staf')
            ->assertOk()
            ->assertSee('Buka Dashboard');

        $html = $response->getContent();

        $adminDashboardRoute = config('features.legacy_admin')
            ? route('admin.dashboard')
            : route('admin.staff.index');

        $this->assertSame(1, substr_count($html, '>Buka Dashboard<'));
        $this->assertSame(1, substr_count($html, 'href="'.$adminDashboardRoute.'"'));
        $this->assertSame(0, substr_count($html, 'href="'.route('login').'"'));
    }

    public function test_owner_sees_one_dashboard_button_linking_to_owner_dashboard(): void
    {
        $owner = User::factory()->owner()->create();

        $response = $this->actingAs($owner)->get('/staf')
            ->assertOk()
            ->assertSee('Buka Dashboard');

        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, '>Buka Dashboard<'));
        $this->assertSame(1, substr_count($html, 'href="'.route('owner.dashboard').'"'));
        $this->assertSame(0, substr_count($html, 'href="'.route('login').'"'));
    }

    public function test_kasir_sees_one_dashboard_button_linking_to_kasir_dashboard(): void
    {
        $kasir = User::factory()->kasir()->create();

        $response = $this->actingAs($kasir)->get('/staf')
            ->assertOk()
            ->assertSee('Buka Dashboard');

        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, '>Buka Dashboard<'));
        $this->assertSame(1, substr_count($html, 'href="'.route('kasir.dashboard').'"'));
        $this->assertSame(0, substr_count($html, 'href="'.route('login').'"'));
    }

    public function test_landing_page_does_not_render_removed_visuals(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertDontSee('Pesanan masuk')
            ->assertDontSee('Laporan hari ini')
            ->assertDontSee('Stok aman')
            ->assertDontSee('Lihat peran')
            ->assertDontSee('Masuk sebagai')
            ->assertDontSee('hero-arch')
            ->assertDontSee('stat-card')
            ->assertDontSee('role-card');
    }

    public function test_landing_page_has_footer_with_location(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('ABS Cafe • Tulungagung');
    }

    public function test_landing_page_uses_indonesian_language(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('Kelola operasional dan pantau bisnis ABS Cafe dalam satu tempat');
    }

    public function test_root_redirects_to_staf_landing(): void
    {
        $this->get('/')->assertRedirect(route('staf.landing'));
    }

    public function test_logout_redirects_to_staf_landing(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/logout')
            ->assertRedirect(route('staf.landing'))
            ->assertSessionHas('success');
    }
}
