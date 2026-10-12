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

    public function test_root_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_admin_can_login_with_email_and_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertNotNull($admin->fresh()->last_login_at);
        $this->assertDatabaseHas('activity_logs', ['user_name' => $admin->name, 'role' => 'admin']);
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
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'admin@example.com',
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
            'email' => 'nonaktif@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'nonaktif@example.com',
            'password' => 'secret123',
        ])->assertRedirect('/login')->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_staf_role_without_portal_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'staf@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'staf',
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'staf@example.com',
            'password' => 'secret123',
        ])->assertRedirect('/login')->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_authenticated_user_visiting_login_is_redirected_to_their_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/login')->assertRedirect(route('admin.dashboard'));
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
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', [
                'login' => 'admin@example.com',
                'password' => 'salah',
            ])->assertStatus(302);
        }

        $this->post('/login', [
            'login' => 'admin@example.com',
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
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', ['login' => 'admin@example.com', 'password' => 'secret123']);

        $this->assertSame(1, ActivityLog::where('activity_type', 'sistem')->count());
    }
}
