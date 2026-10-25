<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SingleLogoutButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_has_exactly_one_logout_form_in_profile_dropdown(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Budi Santoso',
            'username' => 'budiadmin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();

        $content = $response->getContent();

        // 1. Tidak memuat teks ekspresi blade mentah
        $this->assertStringNotContainsString('@{{', $content);
        $this->assertStringNotContainsString('{{ Auth::user()', $content);
        $this->assertStringNotContainsString('{{', $content);

        // 2. Tepat satu form logout di seluruh halaman
        $logoutAction = route('admin.logout');
        $this->assertSame(
            1,
            substr_count($content, 'action="'.$logoutAction.'"'),
            'Halaman Admin harus memuat tepat SATU form logout.'
        );

        // 3. Form logout berada di dalam profile dropdown
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$content);
        $xpath = new DOMXPath($dom);

        $dropdownForm = $xpath->query('//*[@id="profile-dropdown"]//form[@action="'.$logoutAction.'"]');
        $this->assertSame(1, $dropdownForm->length, 'Form logout harus berada di dalam #profile-dropdown.');

        // 4. Pastikan sidebar dan bottom-sheet tidak memuat form/tombol logout
        $sidebarLogout = $xpath->query('//aside//form[@action="'.$logoutAction.'"]');
        $this->assertSame(0, $sidebarLogout->length, 'Sidebar tidak boleh memuat form logout.');

        $sheetLogout = $xpath->query('//*[@id="mobile-more-sheet"]//form[@action="'.$logoutAction.'"]');
        $this->assertSame(0, $sheetLogout->length, 'Bottom sheet tidak boleh memuat form logout.');

        // 5. Isi dropdown profil: nama, username huruf kecil, Ganti Password, Log Aktivitas, Keluar
        $dropdown = $xpath->query('//*[@id="profile-dropdown"]')->item(0);
        $this->assertNotNull($dropdown);
        $dropdownText = $dropdown->textContent;

        $this->assertStringContainsString('Budi Santoso', $dropdownText);
        $this->assertStringContainsString('@budiadmin', $dropdownText);
        $this->assertStringContainsString('Ganti Password', $dropdownText);
        $this->assertStringContainsString('Log Aktivitas', $dropdownText);
        $this->assertStringContainsString('Keluar', $dropdownText);

        // 6. POST logout berhasil redirect ke /login dengan pesan sukses
        $logoutResponse = $this->post($logoutAction);
        $logoutResponse->assertRedirect(route('login'));
        $logoutResponse->assertSessionHas('success', 'Anda telah berhasil keluar.');
        $this->assertGuest();
    }

    public function test_owner_has_exactly_one_logout_form_in_profile_dropdown(): void
    {
        $owner = User::factory()->owner()->create([
            'name' => 'Siti Rahma',
            'username' => 'sitiowner',
        ]);

        $response = $this->actingAs($owner)->get(route('owner.dashboard'));
        $response->assertOk();

        $content = $response->getContent();

        // 1. Tidak memuat teks ekspresi blade mentah
        $this->assertStringNotContainsString('@{{', $content);
        $this->assertStringNotContainsString('{{ Auth::user()', $content);
        $this->assertStringNotContainsString('{{', $content);

        // 2. Tepat satu form logout di seluruh halaman
        $logoutAction = route('owner.logout');
        $this->assertSame(
            1,
            substr_count($content, 'action="'.$logoutAction.'"'),
            'Halaman Owner harus memuat tepat SATU form logout.'
        );

        // 3. Form logout berada di dalam profile dropdown
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$content);
        $xpath = new DOMXPath($dom);

        $dropdownForm = $xpath->query('//*[@id="profile-dropdown"]//form[@action="'.$logoutAction.'"]');
        $this->assertSame(1, $dropdownForm->length, 'Form logout harus berada di dalam #profile-dropdown.');

        // 4. Pastikan sidebar dan bottom-sheet tidak memuat form/tombol logout
        $sidebarLogout = $xpath->query('//aside//form[@action="'.$logoutAction.'"]');
        $this->assertSame(0, $sidebarLogout->length, 'Sidebar tidak boleh memuat form logout.');

        $sheetLogout = $xpath->query('//*[@id="mobile-more-sheet"]//form[@action="'.$logoutAction.'"]');
        $this->assertSame(0, $sheetLogout->length, 'Bottom sheet tidak boleh memuat form logout.');

        // 5. Isi dropdown profil: nama, username huruf kecil, Ganti Password, Keluar (Owner tidak punya Log Aktivitas)
        $dropdown = $xpath->query('//*[@id="profile-dropdown"]')->item(0);
        $this->assertNotNull($dropdown);
        $dropdownText = $dropdown->textContent;

        $this->assertStringContainsString('Siti Rahma', $dropdownText);
        $this->assertStringContainsString('@sitiowner', $dropdownText);
        $this->assertStringContainsString('Ganti Password', $dropdownText);
        $this->assertStringNotContainsString('Log Aktivitas', $dropdownText);
        $this->assertStringContainsString('Keluar', $dropdownText);

        // 6. POST logout berhasil redirect ke /login dengan pesan sukses
        $logoutResponse = $this->post($logoutAction);
        $logoutResponse->assertRedirect(route('login'));
        $logoutResponse->assertSessionHas('success', 'Anda telah berhasil keluar.');
        $this->assertGuest();
    }

    public function test_kasir_has_exactly_one_logout_form_in_profile_dropdown(): void
    {
        $kasir = User::factory()->kasir()->create([
            'name' => 'Andi Pratama',
            'username' => 'andikasir',
        ]);

        $response = $this->actingAs($kasir)->get(route('kasir.dashboard'));
        $response->assertOk();

        $content = $response->getContent();

        // 1. Tidak memuat teks ekspresi blade mentah
        $this->assertStringNotContainsString('@{{', $content);
        $this->assertStringNotContainsString('{{ Auth::user()', $content);
        $this->assertStringNotContainsString('{{', $content);

        // 2. Tepat satu form logout di seluruh halaman
        $logoutAction = route('kasir.logout');
        $this->assertSame(
            1,
            substr_count($content, 'action="'.$logoutAction.'"'),
            'Halaman Kasir harus memuat tepat SATU form logout.'
        );

        // 3. Form logout berada di dalam profile dropdown
        $dom = new DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$content);
        $xpath = new DOMXPath($dom);

        $dropdownForm = $xpath->query('//*[@id="profile-dropdown"]//form[@action="'.$logoutAction.'"]');
        $this->assertSame(1, $dropdownForm->length, 'Form logout harus berada di dalam #profile-dropdown.');

        // 4. Pastikan sidebar dan bottom-sheet tidak memuat form/tombol logout
        $sidebarLogout = $xpath->query('//aside//form[@action="'.$logoutAction.'"]');
        $this->assertSame(0, $sidebarLogout->length, 'Sidebar tidak boleh memuat form logout.');

        $sheetLogout = $xpath->query('//*[@id="mobile-more-sheet"]//form[@action="'.$logoutAction.'"]');
        $this->assertSame(0, $sheetLogout->length, 'Bottom sheet tidak boleh memuat form logout.');

        // 5. Isi dropdown profil: nama, username huruf kecil, Ganti Password, Keluar (Kasir tidak punya Log Aktivitas)
        $dropdown = $xpath->query('//*[@id="profile-dropdown"]')->item(0);
        $this->assertNotNull($dropdown);
        $dropdownText = $dropdown->textContent;

        $this->assertStringContainsString('Andi Pratama', $dropdownText);
        $this->assertStringContainsString('@andikasir', $dropdownText);
        $this->assertStringContainsString('Ganti Password', $dropdownText);
        $this->assertStringNotContainsString('Log Aktivitas', $dropdownText);
        $this->assertStringContainsString('Keluar', $dropdownText);

        // 6. POST logout berhasil redirect ke /login dengan pesan sukses
        $logoutResponse = $this->post($logoutAction);
        $logoutResponse->assertRedirect(route('login'));
        $logoutResponse->assertSessionHas('success', 'Anda telah berhasil keluar.');
        $this->assertGuest();
    }
}
