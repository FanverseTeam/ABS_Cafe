<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Promo;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Utama ABS Cafe',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'status' => 'aktif',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80',
                'last_login_at' => now(),
            ]
        );

        $owner = User::updateOrCreate(
            ['username' => 'owner'],
            [
                'name' => 'Owner ABS Cafe',
                'username' => 'owner',
                'password' => Hash::make('owner123'),
                'role' => 'owner',
                'phone' => '081100112233',
                'status' => 'aktif',
                'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&auto=format&fit=crop&q=80',
                'last_login_at' => now()->subHours(1),
            ]
        );

        $kasir1 = User::updateOrCreate(
            ['username' => 'siti_kasir'],
            [
                'name' => 'Siti Rahma',
                'username' => 'siti_kasir',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'phone' => '082198765432',
                'status' => 'aktif',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&auto=format&fit=crop&q=80',
                'last_login_at' => now()->subHours(2),
            ]
        );

        $kasir2 = User::updateOrCreate(
            ['username' => 'budi_kasir'],
            [
                'name' => 'Budi Santoso',
                'username' => 'budi_kasir',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'phone' => '081377889900',
                'status' => 'aktif',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop&q=80',
                'last_login_at' => now()->subHours(4),
            ]
        );

        $barista = User::updateOrCreate(
            ['email' => 'wahyu.barista@abscafe.com'],
            [
                'name' => 'Wahyu Hidayat',
                'username' => 'wahyu_barista',
                'password' => Hash::make('password'),
                'role' => 'staf',
                'phone' => '085712345678',
                'status' => 'aktif',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&auto=format&fit=crop&q=80',
                'last_login_at' => now()->subHours(6),
            ]
        );

        $kitchen = User::updateOrCreate(
            ['email' => 'dewi.kitchen@abscafe.com'],
            [
                'name' => 'Dewi Lestari',
                'username' => 'dewi_kitchen',
                'password' => Hash::make('password'),
                'role' => 'staf',
                'phone' => '089633445566',
                'status' => 'nonaktif',
                'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&auto=format&fit=crop&q=80',
                'last_login_at' => now()->subDays(3),
            ]
        );

        // 2 & 3. Categories & Menus (Menu Baru Fix ABS Cafe)
        $this->call(MenuUpdateSeeder::class);

        // 4. Tables (20 Meja ABS Cafe)
        for ($i = 1; $i <= 20; $i++) {
            $cap = ($i % 4 === 0) ? 6 : (($i % 2 === 0) ? 4 : 2);
            $loc = ($i <= 8) ? 'Lantai 1 Indoor' : (($i <= 14) ? 'Outdoor Garden' : 'Lantai 2 Mezanin');
            Table::updateOrCreate(
                ['number' => $i],
                [
                    'capacity' => $cap,
                    'status' => 'tersedia',
                    'location_notes' => $loc,
                ]
            );
        }

        // 5. Promos (3 promos)
        $catCoffee = Category::where('name', 'Coffee Base')->first();
        if ($catCoffee) {
            Promo::updateOrCreate(['title' => 'Buy 2 Get 1 Free All Coffee'], [
                'description' => 'Beli 2 cup menu coffee apa saja, dapatkan gratis 1 Americano atau Latte setiap hari!',
                'type' => 'buy_x_get_y',
                'value' => 100,
                'applies_to' => 'kategori',
                'target_category_id' => $catCoffee->id,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'banner' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=800&auto=format&fit=crop&q=80',
                'is_active' => true,
                'min_spend' => 0,
            ]);
        }

        Promo::updateOrCreate(['title' => 'Diskon 20% Pelajar & Mahasiswa Tulungagung'], [
            'description' => 'Tunjukkan kartu pelajar atau KTM kampus Boyolangu/Tulungagung untuk diskon seluruh menu minuman.',
            'type' => 'persen',
            'value' => 20,
            'applies_to' => 'semua',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
            'banner' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&auto=format&fit=crop&q=80',
            'is_active' => true,
            'min_spend' => 30000,
        ]);

        Promo::updateOrCreate(['title' => 'Cashback Rp 10.000 Paket Ngemil Hemat'], [
            'description' => 'Potongan langsung Rp 10.000 untuk pembelian bundling 1 Makanan + 1 Minuman apa saja.',
            'type' => 'nominal',
            'value' => 10000,
            'applies_to' => 'semua',
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(14),
            'banner' => 'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=800&auto=format&fit=crop&q=80',
            'is_active' => true,
            'min_spend' => 45000,
        ]);

        // 6. Cafe Settings
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
            'tax_percent' => '10',
            'service_charge_active' => '0',
            'service_charge_percent' => '5',
            'payment_cash' => '1',
            'payment_qris' => '1',
            'payment_debit' => '1',
            'receipt_header' => "ABS CAFE - ACCESSORIES & CAFE\nJl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219\nTelp: 0896-2010-2010",
            'receipt_footer' => "Terima kasih atas kunjungan Anda!\nFollow Instagram kami: @abs_tulungagung_24\n\"Good Coffee, Better Moments\"",
            'receipt_show_logo' => '1',
            'banner_title' => 'Nikmati Kopi Pilihan & Hidangan Spesial',
            'banner_subtitle' => 'Tempat berkumpul hangat dengan aroma kopi terbaik di Tulungagung',
            'hours_weekday' => '09:00 - 23:00',
            'hours_weekend' => '09:00 - 23:00',
            // Pengaturan bisnis khusus Owner
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

        // 9. Activity Logs
        $activities = [
            ['Kasir Siti Rahma mengonfirmasi lunas pesanan #AB261001 (QRIS)', 'pesanan', 'Siti Rahma', 'kasir', now()->subMinutes(12)],
            ['Kasir Budi mengonfirmasi lunas pesanan #AB261002 (Tunai)', 'pesanan', 'Budi Santoso', 'kasir', now()->subMinutes(25)],
            ['Admin membatalkan pesanan #AB261005: "Pelanggan mendadak pergi"', 'pesanan', 'Admin Utama', 'admin', now()->subHours(1)],
            ['Mengubah harga menu Latte dari Rp 20.000 menjadi Rp 22.000', 'menu', 'Admin Utama', 'admin', now()->subHours(3)],
            ['Mengaktifkan promo "Buy 2 Get 1 Free All Coffee"', 'promo', 'Admin Utama', 'admin', now()->subHours(5)],
            ['Mengubah status meja 10 menjadi nonaktif (perbaikan kursi)', 'meja', 'Admin Utama', 'admin', now()->subDays(1)],
            ['Admin Utama berhasil login ke sistem', 'sistem', 'Admin Utama', 'admin', now()->subHours(8)],
        ];

        foreach ($activities as $act) {
            ActivityLog::create([
                'activity' => $act[0],
                'activity_type' => $act[1],
                'user_name' => $act[2],
                'role' => $act[3],
                'ip_address' => '192.168.1.10',
                'created_at' => $act[4],
            ]);
        }

        // 10. Riwayat Transaksi Historis (60-90 hari) untuk analitik Owner
        $this->call(TransactionSeeder::class);

        // 11. Pendukung Portal Kasir
        $this->call(KasirSeeder::class);
    }
}
