<?php

namespace Tests\Feature\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SessionTimeoutTest extends TestCase
{
    use RefreshDatabase;

    // ─── Kasir: tidak ada batas waktu time out ─────────────────────────

    public function test_kasir_session_does_not_expire_after_inactivity(): void
    {
        $kasir = User::factory()->kasir()->create([
            'username' => 'kasiruser',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'login' => 'kasiruser',
            'password' => 'secret123',
        ])->assertRedirect(route('kasir.dashboard'));

        $this->assertAuthenticatedAs($kasir);

        // Simulasi idle melebihi batas waktu admin (15 menit default)
        session()->put('last_activity_time', now()->subMinutes(60)->timestamp);

        // Kasir tetap bisa mengakses dashboard meskipun idle 60 menit
        $this->get(route('kasir.dashboard'))->assertOk();
        $this->assertAuthenticatedAs($kasir);
    }

    public function test_kasir_login_always_sets_remember_cookie(): void
    {
        User::factory()->kasir()->create([
            'username' => 'kasiruser',
            'password' => Hash::make('secret123'),
        ]);

        // Login tanpa centang remember — kasir tetap mendapat remember cookie
        $response = $this->post('/login', [
            'login' => 'kasiruser',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('kasir.dashboard'));

        $recallerName = Auth::guard('web')->getRecallerName();
        $response->assertCookie($recallerName);
    }

    // ─── Admin: batas waktu time out aktif ──────────────────────────────

    public function test_admin_session_expires_after_inactivity(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'adminuser',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'login' => 'adminuser',
            'password' => 'secret123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);

        // Simulasi idle melebihi batas waktu (default 15 menit)
        session()->put('last_activity_time', now()->subMinutes(20)->timestamp);

        // Admin seharusnya di-redirect ke login
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_admin_session_does_not_expire_before_timeout(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'adminuser',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'login' => 'adminuser',
            'password' => 'secret123',
        ])->assertRedirect(route('admin.dashboard'));

        // Simulasi idle 10 menit (masih di bawah batas 15 menit)
        session()->put('last_activity_time', now()->subMinutes(10)->timestamp);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_timeout_logs_activity(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'adminuser',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'login' => 'adminuser',
            'password' => 'secret123',
        ]);

        $logCountBefore = ActivityLog::where('activity_type', 'sistem')->count();

        // Simulasi idle melebihi batas waktu
        session()->put('last_activity_time', now()->subMinutes(20)->timestamp);

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->assertSame($logCountBefore + 1, ActivityLog::where('activity_type', 'sistem')->count());

        $log = ActivityLog::where('activity_type', 'sistem')->latest('id')->first();
        $this->assertStringContainsString('keluar otomatis', $log->activity);
        $this->assertStringContainsString('keamanan', $log->activity);
    }

    // ─── Owner: batas waktu time out aktif ──────────────────────────────

    public function test_owner_session_expires_after_inactivity(): void
    {
        $owner = User::factory()->owner()->create([
            'username' => 'owneruser',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'login' => 'owneruser',
            'password' => 'secret123',
        ])->assertRedirect(route('owner.dashboard'));

        $this->assertAuthenticatedAs($owner);

        // Simulasi idle melebihi batas waktu
        session()->put('last_activity_time', now()->subMinutes(20)->timestamp);

        $this->get(route('owner.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_owner_session_does_not_expire_before_timeout(): void
    {
        $owner = User::factory()->owner()->create([
            'username' => 'owneruser',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'login' => 'owneruser',
            'password' => 'secret123',
        ])->assertRedirect(route('owner.dashboard'));

        // Simulasi idle 10 menit
        session()->put('last_activity_time', now()->subMinutes(10)->timestamp);

        $this->get(route('owner.dashboard'))->assertOk();
        $this->assertAuthenticatedAs($owner);
    }

    // ─── Session Ping ──────────────────────────────────────────────────

    public function test_session_ping_resets_activity_time(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'adminuser',
            'password' => Hash::make('secret123'),
        ]);

        $this->actingAs($admin);
        session()->put('last_activity_time', now()->subMinutes(10)->timestamp);

        $this->postJson(route('session.ping'))
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        // last_activity_time harus sudah diperbarui ke sekarang
        $lastActivity = session('last_activity_time');
        $this->assertNotNull($lastActivity);
        $this->assertEqualsWithDelta(now()->timestamp, $lastActivity, 5);
    }

    public function test_session_ping_requires_authentication(): void
    {
        $this->postJson(route('session.ping'))->assertStatus(401);
    }

    // ─── Konfigurasi ───────────────────────────────────────────────────

    public function test_admin_timeout_config_exists(): void
    {
        $this->assertSame(15, config('auth.admin_timeout'));
    }

    public function test_session_configuration_expires_when_browser_closes(): void
    {
        $this->assertTrue(config('session.expire_on_close'));
        $this->assertSame(120, config('session.lifetime'));
        $this->assertSame(30 * 24 * 60, config('auth.guards.web.remember'));
    }
}
