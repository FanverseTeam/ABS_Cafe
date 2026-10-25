<?php

namespace Tests\Feature\Admin;

use App\Models\CafeSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Concerns\RequiresLegacyAdmin;
use Tests\TestCase;

#[Group('legacy')]
class SettingTest extends TestCase
{
    use RefreshDatabase;
    use RequiresLegacyAdmin;

    public function test_settings_index_loads_for_tabs(): void
    {
        $this->actingAsAdmin()->get('/admin/pengaturan')->assertOk();
        $this->actingAsAdmin()->get('/admin/pengaturan?tab=pajak')->assertOk();
        $this->actingAsAdmin()->get('/admin/pengaturan?tab=pembayaran')->assertOk();
        $this->actingAsAdmin()->get('/admin/pengaturan?tab=nota')->assertOk();
        $this->actingAsAdmin()->get('/admin/pengaturan?tab=tampilan')->assertOk();
        $this->actingAsAdmin()->get('/admin/pengaturan?tab=akun')->assertOk();
    }

    public function test_admin_can_update_cafe_profile(): void
    {
        $this->actingAsAdmin()->post('/admin/pengaturan/profile', [
            'cafe_name' => 'ABS Cafe Baru',
            'cafe_tagline' => 'Tagline',
            'cafe_address' => 'Alamat',
            'cafe_phone' => '0812',
            'cafe_email' => 'halo@abscafe.com',
            'hours_weekday' => '09:00 - 23:00',
            'hours_weekend' => '09:00 - 23:00',
        ])->assertRedirect(route('admin.settings.index', ['tab' => 'profil']));

        $this->assertSame('ABS Cafe Baru', CafeSetting::get('cafe_name'));
    }

    public function test_admin_can_update_tax_and_service_charge(): void
    {
        $this->actingAsAdmin()->post('/admin/pengaturan/tax', [
            'tax_percent' => 11,
            'service_charge_active' => '1',
            'service_charge_percent' => 5,
        ])->assertRedirect();

        $this->assertSame('11', (string) CafeSetting::get('tax_percent'));
        $this->assertSame('1', CafeSetting::get('service_charge_active'));
    }

    public function test_admin_can_update_payment_settings(): void
    {
        $this->actingAsAdmin()->post('/admin/pengaturan/payment', [
            'payment_cash' => '1',
            'payment_qris' => '1',
        ])->assertRedirect();

        $this->assertSame('1', CafeSetting::get('payment_cash'));
        $this->assertSame('0', CafeSetting::get('payment_debit'));
    }

    public function test_admin_can_update_receipt_settings(): void
    {
        $this->actingAsAdmin()->post('/admin/pengaturan/receipt', [
            'receipt_header' => 'Header Nota',
            'receipt_footer' => 'Footer Nota',
            'receipt_show_logo' => '1',
        ])->assertRedirect();

        $this->assertSame('Header Nota', CafeSetting::get('receipt_header'));
        $this->assertSame('1', CafeSetting::get('receipt_show_logo'));
    }

    public function test_admin_can_update_display_settings(): void
    {
        $this->actingAsAdmin()->post('/admin/pengaturan/display', [
            'banner_title' => 'Judul Banner',
            'banner_subtitle' => 'Subjudul',
        ])->assertRedirect();

        $this->assertSame('Judul Banner', CafeSetting::get('banner_title'));
    }

    public function test_admin_can_update_own_account(): void
    {
        $this->actingAsAdmin();
        $user = auth()->user();

        $this->post('/admin/pengaturan/account', [
            'name' => 'Admin Ganteng',
            'username' => $user->username,
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ])->assertRedirect();

        $user->refresh();
        $this->assertSame('Admin Ganteng', $user->name);
        $this->assertTrue(Hash::check('passwordbaru', $user->password));
    }

    public function test_account_password_requires_confirmation(): void
    {
        $this->actingAsAdmin();
        $user = auth()->user();

        $this->post('/admin/pengaturan/account', [
            'name' => $user->name,
            'username' => $user->username,
            'password' => 'passwordbaru',
            'password_confirmation' => 'beda',
        ])->assertSessionHasErrors('password');
    }

    public function test_account_username_must_be_unique(): void
    {
        $this->actingAsAdmin();
        $user = auth()->user();
        $other = User::factory()->create();

        $this->post('/admin/pengaturan/account', [
            'name' => $user->name,
            'username' => $other->username,
        ])->assertSessionHasErrors('username');
    }
}
