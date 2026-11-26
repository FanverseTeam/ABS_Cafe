<?php

namespace Database\Seeders;

use App\Models\CafeSetting;
use App\Models\Menu;
use App\Models\PaymentMethod;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with real master data only.
     */
    public function run(): void
    {
        // 1. Akun Resmi Operasional ABS Cafe
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Utama ABS Cafe',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '089620102010',
                'status' => 'aktif',
                'avatar' => null,
                'last_login_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['username' => 'owner'],
            [
                'name' => 'Owner ABS Cafe',
                'username' => 'owner',
                'password' => Hash::make('owner123'),
                'role' => 'owner',
                'phone' => '089620102010',
                'status' => 'aktif',
                'avatar' => null,
                'last_login_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir ABS Cafe',
                'username' => 'kasir',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'phone' => '089620102010',
                'status' => 'aktif',
                'avatar' => null,
                'last_login_at' => now(),
            ]
        );

        // Hapus akun staf dummy jika ada
        User::whereNotIn('username', ['admin', 'owner', 'kasir'])->delete();

        // 2. Kategori & Menu Asli ABS Cafe
        $this->call(MenuUpdateSeeder::class);

        // 3. Meja (10 Meja ABS Cafe - status tersedia)
        for ($i = 1; $i <= 10; $i++) {
            $cap = ($i % 4 === 0) ? 6 : (($i % 2 === 0) ? 4 : 2);
            $loc = ($i <= 4) ? 'Lantai 1 Indoor' : (($i <= 7) ? 'Outdoor Garden' : 'Lantai 2 Mezanin');
            Table::updateOrCreate(
                ['number' => $i],
                [
                    'capacity' => $cap,
                    'status' => 'tersedia',
                    'location_notes' => $loc,
                ]
            );
        }

        // 4. Metode Pembayaran (Hanya Tunai)
        PaymentMethod::firstOrCreate(
            ['code' => 'tunai'],
            [
                'name' => 'Tunai',
                'type' => 'cash',
                'description' => 'Pembayaran langsung dengan uang tunai.',
                'instructions' => 'Siapkan uang pas bila memungkinkan.',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
        PaymentMethod::where('code', '!=', 'tunai')->update(['is_active' => false]);

        // 5. Pengaturan Kafe Asli ABS Cafe
        $settings = [
            'cafe_name' => 'ABS Cafe',
            'cafe_tagline' => 'Good Coffee, Better Moments.',
            'cafe_logo' => 'images/logo.png',
            'cafe_address' => 'Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219',
            'cafe_phone' => '0896-2010-2010',
            'cafe_email' => 'halo@abscafe.com',
            'cafe_hours' => 'Buka Setiap Hari 09.00 - 23.00 WIB',
            'cafe_maps_url' => 'https://maps.app.goo.gl/mvEr6sWCacKH5j677',
            'peta_lat' => '-8.0646338',
            'peta_lng' => '111.9111798',
            'last_order' => '22.00 WIB',
            'cafe_instagram' => 'abs_tulungagung_24',
            'cafe_tiktok' => 'abs.tulungagung',
            'whatsapp_nomor' => '6289620102010',
            'whatsapp_pesan' => 'Halo ABS Cafe, saya ingin bertanya.',
            'hero_badge' => 'SELAMAT DATANG DI ABS CAFE',
            'hero_title_prefix' => 'Temukan Momen Terbaik di',
            'hero_title_highlight' => 'ABS Cafe',
            'hero_description' => 'Nikmati kopi berkualitas, makanan lezat, dan suasana nyaman yang cocok untuk bekerja, belajar, atau sekadar bersantai bersama orang-orang tercinta.',
            'about_badge' => 'TENTANG KAMI',
            'about_title' => 'ABS Cafe',
            'about_description' => 'ABS Cafe adalah tempat yang dirancang untuk memberikan pengalaman menikmati kopi terbaik dengan suasana yang hangat dan nyaman. Kami percaya, setiap cangkir kopi bisa menjadi awal dari cerita yang indah.',
            'about_tagline' => 'Good Coffee, Better Moments',
            'feature_1_title' => 'Kopi Berkualitas',
            'feature_2_title' => 'Menu Lezat',
            'feature_3_title' => 'Suasana Nyaman',
            'feature_4_title' => 'Cocok untuk Semua',
            'atmosphere_badge' => 'SUASANA',
            'atmosphere_title' => 'Nyaman & Elegan',
            'atmosphere_description' => 'Desain interior kami yang modern dengan sentuhan alami menciptakan suasana yang hangat dan menenangkan.',
            'location_badge' => 'LOKASI',
            'location_title' => 'Temukan Kami',
            'social_title' => 'Ikuti Kami di Sosial Media',
            'service_charge_active' => '0',
            'service_charge_percent' => '5',
            'payment_cash' => '1',
            'payment_qris' => '0',
            'payment_debit' => '0',
            'receipt_header' => "ABS CAFE - ACCESSORIES & CAFE\nJl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219\nTelp: 0896-2010-2010",
            'receipt_footer' => "Terima kasih atas kunjungan Anda!\nFollow Instagram kami: @abs_tulungagung_24\n\"Good Coffee, Better Moments\"",
            'receipt_show_logo' => '1',
            'banner_title' => 'Nikmati Kopi Pilihan & Hidangan Spesial',
            'banner_subtitle' => 'Tempat berkumpul hangat dengan aroma kopi terbaik di Tulungagung',
            'hours_weekday' => '09:00 - 23:00',
            'hours_weekend' => '09:00 - 23:00',
            'target_daily_sales' => '2000000',
            'target_monthly_sales' => '45000000',
            'profit_margin_percent' => '40',
            'price_rounding' => 'none',
            'notif_daily_report' => '1',
            'notif_weekly_report' => '0',
            'default_opening_cash' => '100000',
            'require_physical_cash_count' => '0',
        ];

        foreach ($settings as $k => $v) {
            CafeSetting::updateOrCreate(['key' => $k], ['value' => $v]);
        }
    }
}
