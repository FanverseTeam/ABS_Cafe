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
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect($this->adminHomeRoute());

        $recallerName = Auth::guard('web')->getRecallerName();
        $response->assertCookieMissing($recallerName);
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
                'route' => config('features.legacy_admin') ? 'admin.dashboard' : 'admin.staff.index',
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
}
