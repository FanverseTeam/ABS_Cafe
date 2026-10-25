<?php

namespace Tests\Feature\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible_for_guests(): void
    {
        $this->get('/login')->assertOk()->assertSee('login', false);
    }

    public function test_remember_checkbox_is_unchecked_by_default_without_hidden_input(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();

        preg_match('/<input[^>]*name="remember"[^>]*>/', $html, $matches);

        $this->assertNotEmpty($matches, 'Checkbox remember tidak ditemukan.');
        $this->assertStringContainsString('name="remember"', $matches[0]);
        $this->assertStringContainsString('value="1"', $matches[0]);
        $this->assertStringNotContainsString('checked', $matches[0]);
        $this->assertStringNotContainsString('x-model', $matches[0]);
        $this->assertStringNotContainsString('wire:model', $matches[0]);
        $this->assertDoesNotMatchRegularExpression(
            '/<input[^>]*type="hidden"[^>]*name="remember"[^>]*>/',
            $html
        );
    }

    public function test_root_returns_customer_landing_page_and_staf_route_shows_splash(): void
    {
        $this->get('/')->assertOk()->assertSee('ABS Cafe');
        $this->get(route('staf.landing'))->assertOk()->assertSee('Selamat Datang');
    }

    public function test_admin_can_login_with_username_and_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'adminuser',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'adminuser',
            'password' => 'secret123',
        ]);

        $response->assertRedirect($this->adminHomeRoute());
        $this->assertAuthenticatedAs($admin);
        $this->assertNotNull($admin->fresh()->last_login_at);
        $this->assertDatabaseHas('log_aktivitas', ['user_name' => $admin->name, 'role' => 'admin']);
    }

    public function test_owner_can_login_with_username(): void
    {
        $owner = User::factory()->owner()->create([
            'username' => 'ownerku',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'login' => 'ownerku',
            'password' => 'secret123',
        ])->assertRedirect(route('owner.dashboard'));

        $this->assertAuthenticatedAs($owner);
    }

    public function test_kasir_can_login_and_is_redirected_to_kasir_dashboard(): void
    {
        $kasir = User::factory()->kasir()->create([
            'username' => 'kasirku',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'login' => 'kasirku',
            'password' => 'secret123',
        ])->assertRedirect(route('kasir.dashboard'));

        $this->assertAuthenticatedAs($kasir);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->admin()->create([
            'username' => 'adminuser',
            'password' => Hash::make('secret123'),
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'adminuser',
            'password' => 'salah',
        ])->assertRedirect('/login')->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_login_requires_fields(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['login', 'password']);
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->admin()->inactive()->create([
            'username' => 'nonaktifuser',
            'password' => Hash::make('secret123'),
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'nonaktifuser',
            'password' => 'secret123',
        ])->assertRedirect('/login')->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_staf_role_without_portal_cannot_login(): void
    {
        User::factory()->create([
            'username' => 'stafuser',
            'password' => Hash::make('secret123'),
            'role' => 'staf',
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'stafuser',
            'password' => 'secret123',
        ])->assertRedirect('/login')->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_authenticated_user_visiting_login_is_redirected_to_their_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/login')->assertRedirect($this->adminHomeRoute());
    }

    public function test_user_can_logout(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_ten_attempts(): void
    {
        User::factory()->admin()->create([
            'username' => 'adminuser',
            'password' => Hash::make('secret123'),
        ]);

        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', [
                'login' => 'adminuser',
                'password' => 'salah',
            ])->assertStatus(302);
        }

        $this->post('/login', [
            'login' => 'adminuser',
            'password' => 'salah',
        ])->assertStatus(429);
    }

    public function test_legacy_login_urls_redirect_to_unified_login(): void
    {
        $this->get('/admin/login')->assertRedirect(route('login'));
        $this->get('/owner/login')->assertRedirect(route('login'));
        $this->get('/kasir/login')->assertRedirect(route('login'));
    }

    public function test_login_activity_is_logged(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'adminuser',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', ['login' => 'adminuser', 'password' => 'secret123']);

        $this->assertSame(1, ActivityLog::where('activity_type', 'sistem')->count());
    }

    public function test_login_page_does_not_contain_demo_section_or_demo_credentials(): void
    {
        $response = $this->get('/login')
            ->assertOk()
            ->assertSee('Masuk ke Dashboard')
            ->assertSee('name="login"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="remember"', false)
            ->assertSee('Lupa kata sandi? Hubungi admin.')
            ->assertDontSee('Akun Demo')
            ->assertDontSee('Gunakan akun ini')
            ->assertDontSee('admin@abscafe.com')
            ->assertDontSee('owner@abscafe.com')
            ->assertDontSee('kasir@abscafe.com')
            ->assertDontSee('admin123')
            ->assertDontSee('owner123')
            ->assertDontSee('kasir123')
            ->assertDontSee('useDemo', false)
            ->assertDontSee('showDemoHint', false);

        $this->assertStringNotContainsString('Akun Demo', $response->getContent());
        $this->assertStringNotContainsString('Gunakan akun ini', $response->getContent());
    }
}
