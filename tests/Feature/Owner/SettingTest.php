<?php

namespace Tests\Feature\Owner;

use App\Models\CafeSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_settings_index_loads(): void
    {
        $this->actingAsOwner()->get('/owner/pengaturan')->assertOk();
    }

    public function test_owner_can_update_business_settings(): void
    {
        $this->actingAsOwner()->post('/owner/pengaturan', [
            'target_daily_sales' => 3000000,
            'target_monthly_sales' => 60000000,
            'profit_margin_percent' => 35,
            'tax_percent' => 11,
            'price_rounding' => '500',
            'default_min_stock' => 15,
            'service_charge_active' => '1',
            'receipt_show_logo' => '1',
        ])->assertRedirect();

        $this->assertSame('3000000', (string) CafeSetting::get('target_daily_sales'));
        $this->assertSame('35', (string) CafeSetting::get('profit_margin_percent'));
        $this->assertSame('1', CafeSetting::get('service_charge_active'));
        $this->assertSame('15', (string) CafeSetting::get('default_min_stock'));
    }

    public function test_owner_settings_validation(): void
    {
        $this->actingAsOwner()->post('/owner/pengaturan', [
            'profit_margin_percent' => 150,
            'price_rounding' => 'sembarang',
        ])->assertSessionHasErrors(['profit_margin_percent', 'price_rounding']);
    }

    public function test_owner_can_update_own_account(): void
    {
        $this->actingAsOwner();
        $owner = auth()->user();

        $this->post('/owner/pengaturan/akun', [
            'name' => 'Owner Baru',
            'username' => $owner->username,
            'email' => $owner->email,
            'phone' => '08123456789',
        ])->assertRedirect();

        $this->assertSame('Owner Baru', $owner->fresh()->name);
    }

    public function test_owner_account_username_must_be_unique(): void
    {
        $this->actingAsOwner();
        $owner = auth()->user();
        $other = User::factory()->create();

        $this->post('/owner/pengaturan/akun', [
            'name' => $owner->name,
            'username' => $other->username,
            'email' => $owner->email,
        ])->assertSessionHasErrors('username');
    }

    public function test_owner_can_change_password(): void
    {
        $this->actingAsOwner();
        $owner = auth()->user();
        $owner->update(['password' => Hash::make('passwordlama')]);

        $this->post('/owner/pengaturan/akun/password', [
            'current_password' => 'passwordlama',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('passwordbaru', $owner->fresh()->password));
    }

    public function test_owner_change_password_fails_with_wrong_current(): void
    {
        $this->actingAsOwner();
        $owner = auth()->user();
        $owner->update(['password' => Hash::make('passwordlama')]);

        $this->post('/owner/pengaturan/akun/password', [
            'current_password' => 'salah',
            'password' => 'passwordbaru',
            'password_confirmation' => 'passwordbaru',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('passwordlama', $owner->fresh()->password));
    }

    public function test_owner_change_password_requires_confirmation(): void
    {
        $this->actingAsOwner();

        $this->post('/owner/pengaturan/akun/password', [
            'current_password' => 'passwordlama',
            'password' => 'passwordbaru',
            'password_confirmation' => 'beda',
        ])->assertSessionHasErrors('password');
    }

    public function test_owner_profile_routes_are_removed(): void
    {
        $this->actingAsOwner()->get('/owner/profil')->assertNotFound();
    }
}
