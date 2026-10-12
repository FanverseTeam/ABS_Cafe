<?php

namespace Tests\Feature\Owner;

use App\Models\CafeSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
