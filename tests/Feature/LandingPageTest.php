<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_staf_landing_page_is_accessible_for_guests_and_shows_splash_elements(): void
    {
        $response = $this->get('/staf')
            ->assertOk()
            ->assertSee('Selamat datang di')
            ->assertSee('ABS Cafe')
            ->assertSee('Good Coffee')
            ->assertSee('Good Mood.')
            ->assertDontSee('Lewati')
            ->assertSee(route('login'));

        $html = $response->getContent();

        // Elemen splash
        $this->assertStringContainsString('splash-logo-container', $html);
        $this->assertStringContainsString('splash-progress-bar', $html);
        $this->assertStringNotContainsString('splash-skip', $html);
    }

    public function test_staf_landing_page_has_correct_meta_tags_and_prefetch(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('<link rel="prefetch" href="'.route('login').'"', false)
            ->assertSee('Portal Staf ABS Cafe', false);
    }

    public function test_staf_landing_page_has_accessibility_live_status(): void
    {
        $response = $this->get('/staf')->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('role="status"', $html);
        $this->assertStringContainsString('aria-live="polite"', $html);
        $this->assertStringContainsString('Memuat halaman login', $html);
        $this->assertStringContainsString('prefers-reduced-motion', $html);
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
            ->assertDontSee('LANDING_BG_URL')
            ->assertDontSee('landing_bg_url');
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

    public function test_guest_does_not_see_skip_button(): void
    {
        $response = $this->get('/staf')
            ->assertOk()
            ->assertDontSee('Lewati');

        $html = $response->getContent();

        $this->assertStringNotContainsString('splash-skip', $html);
        $this->assertStringNotContainsString('Lewati animasi', $html);
    }

    public function test_splash_duration_and_noscript_fallback_follow_config(): void
    {
        config(['splash.duration' => 3.0]);

        $response = $this->get('/staf')->assertOk();
        $html = $response->getContent();

        // Cek noscript meta refresh: 4 detik untuk durasi 3.0 detik
        $this->assertStringContainsString('<noscript>', $html);
        $this->assertStringContainsString('http-equiv="refresh"', $html);
        $this->assertStringContainsString('content="4;url='.route('login').'"', $html);

        // Uji durasi dinamis saat config berubah
        config(['splash.duration' => 5.0]);
        $customResponse = $this->get('/staf')->assertOk();
        $customHtml = $customResponse->getContent();

        $this->assertStringContainsString('content="6;url='.route('login').'"', $customHtml);
    }

    public function test_admin_is_redirected_to_admin_dashboard_without_animation(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/staf')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_owner_is_redirected_to_owner_dashboard_without_animation(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)
            ->get('/staf')
            ->assertRedirect(route('owner.dashboard'));
    }

    public function test_kasir_is_redirected_to_kasir_dashboard_without_animation(): void
    {
        $kasir = User::factory()->kasir()->create();

        $this->actingAs($kasir)
            ->get('/staf')
            ->assertRedirect(route('kasir.dashboard'));
    }

    public function test_direct_login_page_renders_form_without_splash_animation(): void
    {
        $response = $this->get('/login')
            ->assertOk()
            ->assertSee('Masuk')
            ->assertSee('name="login"', false)
            ->assertSee('name="password"', false)
            ->assertDontSee('splash-progress-bar')
            ->assertDontSee('splash-viewport')
            ->assertDontSee('Memuat halaman login');
    }

    public function test_root_returns_customer_landing_page_and_staf_landing_renders_splash(): void
    {
        $this->get('/')->assertOk()->assertSee('ABS Cafe');
        $this->get(route('staf.landing'))->assertOk()->assertSee('Selamat datang di');
    }

    public function test_logout_redirects_to_login_with_success_message(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/logout')
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Anda telah berhasil keluar.');
    }
}
