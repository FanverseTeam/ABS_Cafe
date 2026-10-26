<?php

namespace Tests\Feature\Pelanggan;

use App\Models\CafeSetting;
use App\Models\User;
use App\Support\LandingData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LandingPageCustomerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_guest_can_access_customer_landing_page(): void
    {
        $response = $this->get(route('pelanggan.beranda'));

        $response->assertOk();
        $response->assertSee('Temukan Momen Terbaik di');
        $response->assertSee('ABS Cafe');
        $response->assertSee('Pesan Sekarang');
        $response->assertSee(route('pelanggan.menu'));
        $response->assertSee('Reservasi');
        $response->assertSee(route('pelanggan.reservasi'));
        $response->assertSee('TENTANG KAMI');
        $response->assertSee('Kopi Berkualitas');
        $response->assertSee('Menu Lezat');
        $response->assertSee('Suasana Nyaman');
        $response->assertSee('Cocok untuk Semua');
        $response->assertSee('LOKASI');
        $response->assertSee('Temukan Kami');
        $response->assertDontSee('Login Staf');
        $response->assertDontSee(route('staf.landing'));
    }

    public function test_authenticated_users_from_all_roles_can_access_customer_landing_page(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->owner()->create();
        $kasir = User::factory()->kasir()->create();

        $this->actingAs($admin)->get('/')->assertOk()->assertSee('ABS Cafe');
        $this->actingAs($owner)->get('/')->assertOk()->assertSee('ABS Cafe');
        $this->actingAs($kasir)->get('/')->assertOk()->assertSee('ABS Cafe');
    }

    public function test_menu_and_reservasi_placeholder_pages_return_ok(): void
    {
        $menuResponse = $this->get(route('pelanggan.menu'));
        $menuResponse->assertOk();
        $menuResponse->assertSee('Segera Hadir');
        $menuResponse->assertSee('Menu Kafe');
        $menuResponse->assertSee(route('pelanggan.beranda'));

        $reservasiResponse = $this->get(route('pelanggan.reservasi'));
        $reservasiResponse->assertOk();
        $reservasiResponse->assertSee('Segera Hadir');
        $reservasiResponse->assertSee('Reservasi Meja');
        $reservasiResponse->assertSee(route('pelanggan.beranda'));
    }

    public function test_staf_route_remains_active_and_renders_splash(): void
    {
        $response = $this->get(route('staf.landing'));
        $response->assertOk();
        $response->assertSee('Selamat datang di');
        $response->assertSee('ABS Cafe');
    }

    public function test_landing_page_reflects_settings_changes_and_falls_back_when_empty(): void
    {
        // Set specific values
        CafeSetting::set('cafe_address', 'Jl. Testing Suprapto No. 99, Tulungagung');
        CafeSetting::set('cafe_hours', '09.00 - 23.00 WIB');
        CafeSetting::set('cafe_phone', '0899 9999 9999');
        CafeSetting::set('instagram_handle', 'abs_testing_ig');
        CafeSetting::set('tiktok_handle', 'abs_testing_tiktok');
        Cache::flush();

        $response = $this->get(route('pelanggan.beranda'));
        $response->assertOk();
        $response->assertSee('Jl. Testing Suprapto No. 99, Tulungagung');
        $response->assertSee('09.00 - 23.00 WIB');
        $response->assertSee('0899 9999 9999');
        $response->assertSee('abs_testing_ig');
        $response->assertSee('abs_testing_tiktok');

        // When settings are cleared, fallback should take effect
        CafeSetting::whereIn('key', ['cafe_address', 'cafe_hours', 'cafe_phone', 'alamat', 'jam_operasional', 'telepon', 'instagram_handle', 'tiktok_handle'])->delete();
        Cache::flush();

        $fallbackData = LandingData::get();
        $this->assertEquals('Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219', $fallbackData['cafe']['address']);
        $this->assertEquals('Buka Setiap Hari 09.00 - 23.00 WIB', $fallbackData['cafe']['hours']);
        $this->assertEquals('0896-2010-2010', $fallbackData['cafe']['phone']);
        $this->assertEquals('abs_tulungagung_24', $fallbackData['cafe']['instagram']);

        $fallbackResponse = $this->get(route('pelanggan.beranda'));
        $fallbackResponse->assertOk();
        $fallbackResponse->assertSee('Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219');
    }

    public function test_photo_slot_prioritizes_local_file_then_url(): void
    {
        $testSlot = 'hero';
        $localDir = public_path('images/pelanggan');
        $testFilePath = $localDir.'/hero.jpg';

        File::ensureDirectoryExists($localDir);

        try {
            // 1. Without local file, returns remote URL from config
            if (File::exists($testFilePath)) {
                File::delete($testFilePath);
            }
            $resolvedRemote = LandingData::resolvePhoto($testSlot);
            $this->assertStringContainsString('images.unsplash.com', $resolvedRemote['src']);

            // 2. With local file, returns local asset URL
            File::put($testFilePath, 'fake image content');
            $resolvedLocal = LandingData::resolvePhoto($testSlot);
            $this->assertStringContainsString('images/pelanggan/hero.jpg', $resolvedLocal['src']);
        } finally {
            if (File::exists($testFilePath)) {
                File::delete($testFilePath);
            }
        }
    }

    public function test_landing_page_does_not_contain_emoji(): void
    {
        $response = $this->get(route('pelanggan.beranda'));
        $content = $response->getContent();

        // Regex for detecting common emoji ranges
        $emojiPattern = '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u';

        $this->assertDoesNotMatchRegularExpression($emojiPattern, $content, 'Landing page should not contain emojis.');
    }

    public function test_landing_page_does_not_contain_external_font_or_script_cdns(): void
    {
        $response = $this->get(route('pelanggan.beranda'));
        $content = $response->getContent();

        $this->assertStringNotContainsString('fonts.googleapis.com', $content);
        $this->assertStringNotContainsString('fonts.gstatic.com', $content);
        $this->assertStringNotContainsString('cdnjs.cloudflare.com', $content);
        $this->assertStringNotContainsString('cdn.jsdelivr.net', $content);
        $this->assertStringNotContainsString('unpkg.com', $content);
    }

    public function test_customer_pages_do_not_contain_staff_links(): void
    {
        foreach ([route('pelanggan.beranda'), route('pelanggan.menu'), route('pelanggan.reservasi')] as $url) {
            $content = $this->get($url)->assertOk()->getContent();
            $this->assertStringNotContainsString('Login Staf', $content);
            $this->assertStringNotContainsString('/staf', $content);
            $this->assertStringNotContainsString('/admin', $content);
            $this->assertStringNotContainsString('/kasir', $content);
            $this->assertStringNotContainsString('/owner', $content);
        }
    }

    public function test_customer_pages_do_not_load_staf_vite_entries(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        // Customer hanya memuat entry pelanggan, bukan app.css/app.js staf
        $this->assertStringNotContainsString('app.css', $content);
        $this->assertStringNotContainsString('app.js', $content);
        $this->assertStringContainsString('pelanggan', $content);
    }

    public function test_staf_pages_have_noindex_header(): void
    {
        foreach ([route('staf.landing'), route('login')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        }
    }

    public function test_robots_txt_disallows_staf_paths(): void
    {
        $response = $this->get('/robots.txt')->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Disallow: /staf', $content);
        $this->assertStringContainsString('Disallow: /login', $content);
        $this->assertStringContainsString('Disallow: /admin', $content);
    }

    public function test_landing_page_renders_real_location_contact_and_maps_url(): void
    {
        Cache::flush();
        $response = $this->get(route('pelanggan.beranda'));
        $response->assertOk();

        // Alamat dan telepon asli
        $response->assertSee('Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219');
        $response->assertSee('0896-2010-2010');
        $response->assertSee('href="tel:+6289620102010"', false);

        // Tautan Google Maps resmi baru dan atribut keamanan
        $response->assertSee('href="https://maps.app.goo.gl/mvEr6sWCacKH5j677"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('target="_blank"', false);

        // Jam operasional
        $response->assertSee('09.00 - 23.00 WIB');

        // Label peta SVG
        $response->assertSee('Jl. Letjend Suprapto');
    }

    public function test_json_ld_schema_contains_structured_real_address_and_coordinates(): void
    {
        Cache::flush();
        $content = $this->get(route('pelanggan.beranda'))->assertOk()->getContent();

        $this->assertStringContainsString('"@type": "CafeOrCoffeeShop"', $content);
        $this->assertStringContainsString('"streetAddress": "Jl. Letjend Suprapto No.44"', $content);
        $this->assertStringContainsString('"addressLocality": "Tulungagung"', $content);
        $this->assertStringContainsString('"addressRegion": "Jawa Timur"', $content);
        $this->assertStringContainsString('"postalCode": "66219"', $content);
        $this->assertStringContainsString('"telephone": "0896-2010-2010"', $content);
        $this->assertStringContainsString('"latitude": -8.0646338', $content);
        $this->assertStringContainsString('"longitude": 111.9111798', $content);
        $this->assertStringContainsString('"openingHours": "Mo-Su 09:00-23:00"', $content);
    }

    public function test_admin_can_update_website_settings_and_reflects_on_landing_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $customAddress = 'Jl. Letjend Suprapto No. 99, Tulungagung 66219';
        $customPhone = '0896-9999-8888';
        $customMaps = 'https://maps.app.goo.gl/testCustomABCxyz';

        $this->actingAs($admin)
            ->post(route('admin.website.profile'), [
                'cafe_name' => 'ABS Cafe Baru',
                'cafe_tagline' => 'Fresh Tagline',
                'cafe_address' => $customAddress,
                'cafe_phone' => $customPhone,
                'cafe_email' => 'admin@abscafe.com',
                'cafe_maps_url' => $customMaps,
                'hours_weekday' => '10:00 - 22:00',
                'hours_weekend' => '10:00 - 23:00',
            ])
            ->assertRedirect();

        Cache::flush();

        $response = $this->get(route('pelanggan.beranda'));
        $response->assertOk();
        $response->assertSee($customAddress);
        $response->assertSee($customPhone);
        $response->assertSee('href="tel:+6289699998888"', false);
        $response->assertSee('href="'.e($customMaps).'"', false);
    }

    public function test_coordinate_validation_rejects_out_of_range_values(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->post(route('admin.website.profile'), [
                'cafe_name' => 'ABS Cafe',
                'cafe_tagline' => 'Accessories & Cafe',
                'cafe_address' => 'Jl. Letjend Suprapto No.44',
                'cafe_phone' => '0896-2010-2010',
                'cafe_email' => 'halo@abscafe.com',
                'peta_lat' => '999',
                'peta_lng' => '0',
                'hours_weekday' => '09:00 - 23:00',
                'hours_weekend' => '09:00 - 23:00',
            ])
            ->assertSessionHasErrors('peta_lat');
    }

    public function test_maps_url_validation_rejects_non_google_domain(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->post(route('admin.website.profile'), [
                'cafe_name' => 'ABS Cafe',
                'cafe_tagline' => 'Accessories & Cafe',
                'cafe_address' => 'Jl. Letjend Suprapto No.44',
                'cafe_phone' => '0896-2010-2010',
                'cafe_email' => 'halo@abscafe.com',
                'cafe_maps_url' => 'https://openstreetmap.org/some-location',
                'hours_weekday' => '09:00 - 23:00',
                'hours_weekend' => '09:00 - 23:00',
            ])
            ->assertSessionHasErrors('cafe_maps_url');
    }

    public function test_migration_does_not_overwrite_admin_customized_settings(): void
    {
        $adminCustomAddress = 'Jl. Khusus Milik Admin No. 10, Tulungagung';
        CafeSetting::set('cafe_address', $adminCustomAddress);

        $migration = require database_path('migrations/2026_10_25_143500_perbarui_data_lokasi_kontak_abs_cafe.php');
        $migration->up();

        $this->assertEquals($adminCustomAddress, CafeSetting::get('cafe_address'));
    }

    public function test_whatsapp_button_renders_correct_wa_me_link(): void
    {
        Cache::flush();
        CafeSetting::set('whatsapp_nomor', '6289620102010');
        CafeSetting::set('whatsapp_pesan', 'Halo ABS Cafe, saya ingin bertanya.');
        Cache::flush();

        $response = $this->get(route('pelanggan.beranda'))->assertOk();

        $expectedUrl = 'https://wa.me/6289620102010?text='.rawurlencode('Halo ABS Cafe, saya ingin bertanya.');
        $response->assertSee('href="'.e($expectedUrl).'"', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('Chat via WhatsApp');
    }

    public function test_whatsapp_number_normalisation_formats(): void
    {
        $cases = [
            '08962010-2010' => '6289620102010',
            '6289620102010' => '6289620102010',
            '+6289620102010' => '6289620102010',
        ];

        foreach ($cases as $input => $expected) {
            Cache::flush();
            CafeSetting::set('whatsapp_nomor', $input);
            Cache::flush();

            $data = LandingData::build();
            $this->assertSame(
                $expected,
                $data['cafe']['whatsapp_nomor'],
                "Format '$input' seharusnya dinormalisasi menjadi '$expected'"
            );
            $this->assertStringContainsString(
                'wa.me/'.$expected,
                $data['cafe']['whatsapp_url'] ?? '',
                "URL WhatsApp untuk format '$input' harus berisi '$expected'"
            );
        }
    }

    public function test_whatsapp_button_hidden_when_number_empty(): void
    {
        Cache::flush();
        CafeSetting::set('whatsapp_nomor', '');
        Cache::flush();

        $response = $this->get(route('pelanggan.beranda'))->assertOk();
        $response->assertDontSee('wa.me', false);
        $response->assertDontSee('Chat via WhatsApp');
    }

    public function test_admin_can_update_whatsapp_settings_and_reflects_on_landing(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->post(route('admin.website.profile'), [
                'cafe_name' => 'ABS Cafe',
                'cafe_tagline' => 'Accessories & Cafe',
                'cafe_address' => 'Jl. Letjend Suprapto No.44',
                'cafe_phone' => '0896-2010-2010',
                'cafe_email' => 'halo@abscafe.com',
                'whatsapp_nomor' => '0896-2010-2010',
                'whatsapp_pesan' => 'Halo, mau tanya menu ya.',
                'hours_weekday' => '09:00 - 23:00',
                'hours_weekend' => '09:00 - 23:00',
            ])
            ->assertRedirect();

        Cache::flush();
        $response = $this->get(route('pelanggan.beranda'))->assertOk();

        $expectedUrl = 'https://wa.me/6289620102010?text='.rawurlencode('Halo, mau tanya menu ya.');
        $response->assertSee('href="'.e($expectedUrl).'"', false);
    }

    public function test_whatsapp_validation_rejects_invalid_number(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->post(route('admin.website.profile'), [
                'cafe_name' => 'ABS Cafe',
                'cafe_tagline' => 'Accessories & Cafe',
                'cafe_address' => 'Jl. Letjend Suprapto No.44',
                'cafe_phone' => '0896-2010-2010',
                'cafe_email' => 'halo@abscafe.com',
                'whatsapp_nomor' => '12345',
                'hours_weekday' => '09:00 - 23:00',
                'hours_weekend' => '09:00 - 23:00',
            ])
            ->assertSessionHasErrors('whatsapp_nomor');
    }
}
