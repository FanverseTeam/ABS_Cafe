<?php

namespace Tests\Feature;

use App\Models\CafeSetting;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class LogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_logo_assets_are_removed_and_not_referenced(): void
    {
        $this->assertFileExists(base_path('Logo.png'));
        $this->assertFileExists(public_path('images/logo.png'));
        $this->assertFileDoesNotExist(public_path('images/Logo.png'));
        $this->assertFileDoesNotExist(public_path('Logo.png'));

        $directories = [
            resource_path('views'),
            app_path(),
            database_path(),
            base_path('routes'),
        ];

        foreach ($directories as $directory) {
            foreach (File::allFiles($directory) as $file) {
                if (! in_array($file->getExtension(), ['php', 'json', 'js', 'css', 'sql'], true)) {
                    continue;
                }

                $contents = File::get($file->getPathname());

                $this->assertStringNotContainsString('images/Logo.png', $contents, $file->getRelativePathname());
                $this->assertStringNotContainsString('public/Logo.png', $contents, $file->getRelativePathname());
            }
        }
    }

    public function test_logo_and_favicon_assets_exist_and_are_readable(): void
    {
        foreach ([
            'images/logo.png',
            'images/logo-badge.png',
            'images/logo-og.png',
            'favicon.ico',
        ] as $asset) {
            $path = public_path($asset);

            $this->assertFileExists($path);
            $this->assertTrue(is_readable($path), "Asset [{$asset}] is not readable.");
            $this->assertGreaterThan(0, filesize($path), "Asset [{$asset}] is empty.");
        }
    }

    public function test_logo_component_renders_on_login_page(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('abs-cafe-logo', false)
            ->assertSee('alt="ABS Cafe"', false)
            ->assertSee('images/logo.png', false)
            ->assertSee('background-color:transparent', false);
    }

    public function test_logo_component_renders_on_staf_landing_page(): void
    {
        $this->get('/staf')
            ->assertOk()
            ->assertSee('abs-cafe-logo', false)
            ->assertSee('alt="ABS Cafe"', false)
            ->assertSee('images/logo.png', false)
            ->assertSee('images/logo-badge.png', false)
            ->assertSee('background-color:#290024', false);
    }

    public function test_missing_configured_logo_falls_back_to_canonical_logo(): void
    {
        CafeSetting::set('cafe_logo', 'images/logo-yang-tidak-ada.png');

        $this->get('/staf')
            ->assertOk()
            ->assertSee('images/logo.png', false)
            ->assertDontSee('images/logo-yang-tidak-ada.png', false);
    }

    public function test_logo_component_uses_configured_logo(): void
    {
        CafeSetting::set('cafe_logo', 'images/logo-badge.png');

        $this->get('/staf')
            ->assertOk()
            ->assertSee('src="'.asset('images/logo-badge.png').'"', false);
    }

    public function test_logo_component_renders_on_each_role_layout(): void
    {
        $this->actingAsAdmin()->get($this->adminHomePath())
            ->assertOk()
            ->assertSee('abs-cafe-logo', false)
            ->assertSee('alt="ABS Cafe"', false)
            ->assertSee('background-color:transparent', false);

        $this->actingAsOwner()->get('/owner')
            ->assertOk()
            ->assertSee('abs-cafe-logo', false)
            ->assertSee('alt="ABS Cafe"', false)
            ->assertSee('background-color:transparent', false);

        $this->actingAsKasir()->get('/kasir')
            ->assertOk()
            ->assertSee('abs-cafe-logo', false)
            ->assertSee('alt="ABS Cafe"', false)
            ->assertSee('background-color:transparent', false);
    }

    public function test_logo_component_renders_on_payment_receipt(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $order = Order::factory()->paid()->create();

        $this->get(route('kasir.payments.receipt', $order))
            ->assertOk()
            ->assertSee('abs-cafe-logo', false)
            ->assertSee('alt="ABS Cafe"', false)
            ->assertSee('background-color:#000000', false);
    }

    public function test_logo_component_renders_on_shift_receipt(): void
    {
        $this->actingAsKasir();
        $shift = $this->openShift();

        $this->get(route('kasir.shift.receipt', $shift))
            ->assertOk()
            ->assertSee('abs-cafe-logo', false)
            ->assertSee('alt="ABS Cafe"', false)
            ->assertSee('background-color:#000000', false);
    }

    public function test_logo_component_renders_on_report_print(): void
    {
        $this->actingAsOwner()
            ->get(route('owner.reports.print'))
            ->assertOk()
            ->assertSee('abs-cafe-logo', false)
            ->assertSee('alt="ABS Cafe"', false)
            ->assertSee('background-color:#000000', false);
    }

    #[Group('legacy')]
    public function test_logo_component_renders_on_admin_order_receipt(): void
    {
        if (! config('features.legacy_admin')) {
            $this->markTestSkipped('Legacy admin routes are disabled.');
        }

        $this->actingAsAdmin()
            ->get('/admin/pesanan')
            ->assertOk()
            ->assertSee('abs-cafe-logo', false)
            ->assertSee('alt="ABS Cafe"', false)
            ->assertSee('background-color:#000000', false);
    }

    public function test_admin_settings_use_canonical_logo_default(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.website.index'))
            ->assertOk()
            ->assertSee('name="cafe_logo"', false)
            ->assertSee('value="images/logo.png"', false)
            ->assertSee('abs-cafe-logo', false);
    }
}
