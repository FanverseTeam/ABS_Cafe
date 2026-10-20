<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RememberMeTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_remember_sets_recaller_cookie_and_stores_remember_token(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@example.com',
            'password' => 'secret123',
            'remember' => '1',
        ]);

        $response->assertRedirect($this->adminHomeRoute());

        $recallerName = Auth::guard('web')->getRecallerName();
        $response->assertCookie($recallerName);

        $cookie = $response->getCookie($recallerName);
        $this->assertNotNull($cookie);

        // Durasi cookie remember 30 hari (30 * 24 * 3600 = 2.592.000 detik)
        $diffInSeconds = $cookie->getExpiresTime() - time();
        $this->assertEqualsWithDelta(30 * 24 * 3600, $diffInSeconds, 10);

        // Pastikan remember_token tersimpan di database
        $this->assertNotNull($admin->fresh()->remember_token);
        $this->assertNotEmpty($admin->fresh()->remember_token);
    }

    public function test_login_without_remember_does_not_set_recaller_cookie(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'remember_token' => null,
        ]);

        $this->assertNull($admin->fresh()->remember_token);

        $response = $this->post('/login', [
            'login' => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect($this->adminHomeRoute());

        $recallerName = Auth::guard('web')->getRecallerName();
        $response->assertCookieMissing($recallerName);
        $this->assertNull($admin->fresh()->remember_token);
    }

    public function test_login_without_remember_clears_previous_recaller_cookie_and_rotates_token(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $firstLogin = $this->post('/login', [
            'login' => 'admin@example.com',
            'password' => 'secret123',
            'remember' => '1',
        ]);

        $recallerName = Auth::guard('web')->getRecallerName();
        $oldCookie = $firstLogin->getCookie($recallerName);
        $oldToken = $admin->fresh()->remember_token;

        $this->assertNotNull($oldCookie);
        $this->assertNotNull($oldToken);

        $this->flushSession();
        Auth::forgetGuards();

        $secondLogin = $this->withCookie($recallerName, $oldCookie->getValue())
            ->post('/login', [
                'login' => 'admin@example.com',
                'password' => 'secret123',
            ]);

        $secondLogin->assertRedirect($this->adminHomeRoute());
        $secondLogin->assertCookieExpired($recallerName);

        $newToken = $admin->fresh()->remember_token;
        $this->assertNotNull($newToken);
        $this->assertNotSame($oldToken, $newToken);

        $this->flushSession();
        Auth::forgetGuards();

        $this->withCookie($recallerName, $oldCookie->getValue())
            ->get($this->adminHomePath())
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_session_loss_without_remember_redirects_to_login(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login', [
            'login' => 'admin@example.com',
            'password' => 'secret123',
        ])->assertRedirect($this->adminHomeRoute());

        $this->assertAuthenticatedAs($admin);

        $this->flushSession();
        Auth::forgetGuards();

        $this->get($this->adminHomePath())
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_session_configuration_expires_when_browser_closes_and_requires_idle_timeout(): void
    {
        $this->assertTrue(config('session.expire_on_close'));
        $this->assertSame(120, config('session.lifetime'));
        $this->assertSame(30 * 24 * 60, config('auth.guards.web.remember'));
    }

    public function test_clearing_session_cookie_keeps_user_authenticated_via_remember_cookie(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $loginResponse = $this->post('/login', [
            'login' => 'admin@example.com',
            'password' => 'secret123',
            'remember' => '1',
        ]);

        $recallerName = Auth::guard('web')->getRecallerName();
        $recallerCookie = $loginResponse->getCookie($recallerName);
        $this->assertNotNull($recallerCookie);

        // Hapus session untuk simulasi browser ditutup / sesi berakhir
        $this->flushSession();
        Auth::forgetGuards();

        // Mengakses halaman terproteksi dengan membawa cookie remember saja
        $response = $this->withCookie($recallerName, $recallerCookie->getValue())
            ->get($this->adminHomeRoute());

        $response->assertOk();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_logout_invalidates_remember_cookie_and_cycles_remember_token(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $loginResponse = $this->post('/login', [
            'login' => 'admin@example.com',
            'password' => 'secret123',
            'remember' => '1',
        ]);

        $recallerName = Auth::guard('web')->getRecallerName();
        $recallerCookie = $loginResponse->getCookie($recallerName);
        $initialToken = $admin->fresh()->remember_token;
        $this->assertNotNull($initialToken);

        // Logout
        $logoutResponse = $this->withCookie($recallerName, $recallerCookie->getValue())
            ->post('/admin/logout');

        $logoutResponse->assertRedirect(route('staf.landing'));
        $logoutResponse->assertCookieExpired($recallerName);
        $this->assertGuest();

        // remember_token harus diganti (cycled)
        $this->assertNotEquals($initialToken, $admin->fresh()->remember_token);

        // Pastikan cookie remember tidak lagi berlaku setelah logout
        $this->flushSession();
        Auth::forgetGuards();

        $this->withCookie($recallerName, $recallerCookie->getValue())
            ->get($this->adminHomeRoute())
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_inactive_account_with_remember_cookie_is_rejected(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
            'status' => 'aktif',
        ]);

        $loginResponse = $this->post('/login', [
            'login' => 'admin@example.com',
            'password' => 'secret123',
            'remember' => '1',
        ]);

        $recallerName = Auth::guard('web')->getRecallerName();
        $recallerCookie = $loginResponse->getCookie($recallerName);

        // Akun dinonaktifkan
        $admin->update(['status' => 'nonaktif']);

        // Hapus sesi dan coba masuk hanya dengan cookie remember
        $this->flushSession();
        Auth::forgetGuards();

        $response = $this->withCookie($recallerName, $recallerCookie->getValue())
            ->get($this->adminHomeRoute());

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();

        // Kunjungan ke halaman login juga tidak boleh langsung terautentikasi
        $this->flushSession();
        Auth::forgetGuards();

        $this->withCookie($recallerName, $recallerCookie->getValue())
            ->get(route('login'))
            ->assertOk();

        $this->assertGuest();
    }

    public function test_password_change_invalidates_remember_cookie(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $loginResponse = $this->post('/login', [
            'login' => 'admin@example.com',
            'password' => 'secret123',
            'remember' => '1',
        ]);

        $recallerName = Auth::guard('web')->getRecallerName();
        $recallerCookie = $loginResponse->getCookie($recallerName);

        // Ganti password
        $admin->update(['password' => Hash::make('passwordbaru123')]);

        // Hapus sesi
        $this->flushSession();
        Auth::forgetGuards();

        // Request dengan cookie recaller lama harus ditolak
        $response = $this->withCookie($recallerName, $recallerCookie->getValue())
            ->get($this->adminHomeRoute());

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_remember_me_works_for_all_roles_with_correct_redirects(): void
    {
        $roles = [
            'admin' => [
                'factory' => 'admin',
                'route' => 'admin.dashboard',
            ],
            'owner' => ['factory' => 'owner', 'route' => 'owner.dashboard'],
            'kasir' => ['factory' => 'kasir', 'route' => 'kasir.dashboard'],
        ];

        foreach ($roles as $role => $config) {
            $user = User::factory()->{$config['factory']}()->create([
                'username' => $role.'_user',
                'password' => Hash::make('secret123'),
                'status' => 'aktif',
            ]);

            $response = $this->post('/login', [
                'login' => $role.'_user',
                'password' => 'secret123',
                'remember' => '1',
            ]);

            $response->assertRedirect(route($config['route']));

            $recallerName = Auth::guard('web')->getRecallerName();
            $recallerCookie = $response->getCookie($recallerName);
            $this->assertNotNull($recallerCookie, "Recaller cookie must be set for role {$role}");

            // Hapus session
            $this->flushSession();
            Auth::forgetGuards();

            // Masuk kembali menggunakan cookie ke dashboard masing-masing
            $dashboardResponse = $this->withCookie($recallerName, $recallerCookie->getValue())
                ->get(route($config['route']));

            $dashboardResponse->assertOk();
            $this->assertAuthenticatedAs($user);

            // Buka halaman /login saat sudah terautentikasi via cookie -> redirect ke dashboard role terkait
            $this->flushSession();
            Auth::forgetGuards();

            $loginRedirectResponse = $this->withCookie($recallerName, $recallerCookie->getValue())
                ->get(route('login'));

            $loginRedirectResponse->assertRedirect(route($config['route']));

            // Logout dan bersihkan state
            $this->post('/admin/logout');
            $this->flushSession();
            Auth::forgetGuards();
        }
    }

    public function test_logout_invalidates_cookie_and_token_for_all_roles(): void
    {
        $roles = [
            'admin' => [
                'factory' => 'admin',
                'email' => 'admin_logout@example.com',
                'logout' => 'admin.logout',
                'home' => 'admin.dashboard',
                'redirect' => 'admin.login',
            ],
            'owner' => [
                'factory' => 'owner',
                'email' => 'owner_logout@example.com',
                'logout' => 'owner.logout',
                'home' => 'owner.dashboard',
                'redirect' => 'login',
            ],
            'kasir' => [
                'factory' => 'kasir',
                'email' => 'kasir_logout@example.com',
                'logout' => 'kasir.logout',
                'home' => 'kasir.dashboard',
                'redirect' => 'login',
            ],
        ];

        foreach ($roles as $role => $config) {
            $user = User::factory()->{$config['factory']}()->create([
                'email' => $config['email'],
                'password' => Hash::make('secret123'),
            ]);

            $loginResponse = $this->post('/login', [
                'login' => $config['email'],
                'password' => 'secret123',
                'remember' => '1',
            ]);

            $loginResponse->assertRedirect(route($config['home']));

            $recallerName = Auth::guard('web')->getRecallerName();
            $recallerCookie = $loginResponse->getCookie($recallerName);
            $initialToken = $user->fresh()->remember_token;

            $this->assertNotNull($recallerCookie, "Recaller cookie harus dibuat untuk {$role}.");
            $this->assertNotNull($initialToken);

            $this->flushSession();
            Auth::forgetGuards();

            $logoutResponse = $this->withCookie($recallerName, $recallerCookie->getValue())
                ->post(route($config['logout']));

            $logoutResponse->assertRedirect(route('staf.landing'));
            $logoutResponse->assertCookieExpired($recallerName);
            $this->assertGuest();
            $this->assertNotSame($initialToken, $user->fresh()->remember_token);

            $this->flushSession();
            Auth::forgetGuards();

            $this->withCookie($recallerName, $recallerCookie->getValue())
                ->get(route($config['home']))
                ->assertRedirect(route($config['redirect']));

            $this->assertGuest();

            $this->flushSession();
            Auth::forgetGuards();
        }
    }
}
