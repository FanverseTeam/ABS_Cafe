<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTimeline;
use App\Models\Promo;
use App\Models\StockLog;
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

        // 4. Tables (10 tables)
        for ($i = 1; $i <= 10; $i++) {
            $cap = ($i % 3 === 0) ? 6 : (($i % 2 === 0) ? 4 : 2);
            $status = ($i === 2 || $i === 4) ? 'terpakai' : (($i === 10) ? 'nonaktif' : 'tersedia');
            $loc = ($i <= 4) ? 'Lantai 1 Indoor' : (($i <= 7) ? 'Outdoor Garden' : 'Lantai 2 Mezanin');
            Table::updateOrCreate(
                ['number' => $i],
                [
                    'capacity' => $cap,
                    'status' => $status,
                    'location_notes' => $loc,
                ]
            );
        }

        // 5. Orders (25 realistic orders)
        $sampleCustomers = [
            'Rian Pratama', 'Dinda Ayu', 'Fajar Nugraha', 'Nadia Salsabila',
            'Bagas Aditya', 'Clara Anindya', 'Dimas Wicaksono', 'Mega Putri',
            'Hendra Setiawan', 'Anisa Maulida', 'Yogi Saputra', 'Kurnia Sandy',
            'Bayu Pamungkas', 'Tiara Anggraini', 'Rizky Firmansyah', 'Salsabila Putri',
            'Zaky Alfarizi', 'Nadira Ramadhani', 'Ilham Prasetyo', 'Novi Safitri',
            'Doni Kusuma', 'Rani Octaviana', 'Eko Purnomo', 'Vina Melati', 'Arif Budiman',
        ];

        $paymentMethods = ['qris', 'tunai', 'debit'];
        $menuList = Menu::with('variants')->get();

        foreach ($sampleCustomers as $index => $customer) {
            $orderCode = sprintf('#AB2610%02d', $index + 1);
            $isDineIn = ($index % 5 !== 3);
            $tableNum = $isDineIn ? (($index % 9) + 1) : null;
            $type = $isDineIn ? 'dine_in' : 'take_away';
            $method = $paymentMethods[$index % 3];

            // Variasi status
            $payStatus = 'lunas';
            $orderStatus = 'selesai';
            $cancelReason = null;
            $cashier = ($index % 2 === 0) ? 'Siti Rahma' : 'Budi Santoso';
            $paidAt = now()->subHours(25 - $index);

            if ($index === 0) {
                // Pesanan sedang diproses hari ini
                $payStatus = 'lunas';
                $orderStatus = 'diproses';
                $paidAt = now()->subMinutes(15);
            } elseif ($index === 1) {
                $payStatus = 'lunas';
                $orderStatus = 'siap';
                $paidAt = now()->subMinutes(25);
            } elseif ($index === 3) {
                $payStatus = 'menunggu_pembayaran';
                $orderStatus = 'diproses';
                $paidAt = null;
                $cashier = null;
            } elseif ($index === 4) {
                $payStatus = 'dibatalkan';
                $orderStatus = 'selesai';
                $cancelReason = 'Pelanggan membatalkan pesanan karena urusan mendadak.';
            }

            // Pilih 1 - 3 menu random
            $itemCount = ($index % 3) + 1;
            $subtotal = 0;
            $selectedMenus = $menuList->slice(($index * 2) % 12, $itemCount);

            $order = Order::updateOrCreate(
                ['order_code' => $orderCode],
                [
                    'customer_name' => $customer,
                    'table_number' => $tableNum,
                    'order_type' => $type,
                    'payment_method' => $method,
                    'payment_status' => $payStatus,
                    'order_status' => $orderStatus,
                    'cashier_name' => $cashier,
                    'paid_at' => $paidAt,
                    'cancel_reason' => $cancelReason,
                    'created_at' => now()->subHours(26 - $index),
                ]
            );

            // Create items
            $order->items()->delete();
            foreach ($selectedMenus as $menu) {
                $qty = ($index % 2 === 0) ? 1 : 2;
                $variant = $menu->variants->where('is_available', true)->first() ?? $menu->variants->first();
                $itemPrice = $variant ? (float) $variant->price : (float) $menu->price;
                $itemSubtotal = $itemPrice * $qty;
                $subtotal += $itemSubtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'variant_id' => $variant?->id,
                    'name' => $menu->orderLineName($variant),
                    'price' => $itemPrice,
                    'quantity' => $qty,
                    'subtotal' => $itemSubtotal,
                    'notes' => ($index % 4 === 0) ? 'Less sweet, ice dipisah' : null,
                ]);
            }

            $tax = $subtotal * 0.10;
            $discount = ($index === 3) ? 10000 : 0;
            $total = $subtotal + $tax - $discount;

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
            ]);

            // Timeline
            $order->timelines()->delete();
            OrderTimeline::create([
                'order_id' => $order->id,
                'status' => 'DIBUAT',
                'actor' => 'Sistem Meja',
                'description' => "Pesanan dibuat dari {$type} ".($tableNum ? "Meja {$tableNum}" : ''),
                'logged_at' => $order->created_at,
            ]);

            if ($payStatus === 'lunas') {
                OrderTimeline::create([
                    'order_id' => $order->id,
                    'status' => 'LUNAS',
                    'actor' => $cashier,
                    'description' => "Pembayaran {$method} lunas diverifikasi kasir",
                    'logged_at' => $paidAt,
                ]);
            }

            if ($orderStatus === 'siap' || $orderStatus === 'selesai') {
                OrderTimeline::create([
                    'order_id' => $order->id,
                    'status' => 'SIAP',
                    'actor' => 'Barista / Kitchen',
                    'description' => 'Pesanan siap disajikan ke meja',
                    'logged_at' => $paidAt ? $paidAt->addMinutes(10) : now(),
                ]);
            }

            if ($orderStatus === 'selesai') {
                OrderTimeline::create([
                    'order_id' => $order->id,
                    'status' => 'SELESAI',
                    'actor' => 'Staf Floor',
                    'description' => 'Pelanggan telah menikmati hidangan',
                    'logged_at' => $paidAt ? $paidAt->addMinutes(35) : now(),
                ]);
            }

            if ($payStatus === 'dibatalkan') {
                OrderTimeline::create([
                    'order_id' => $order->id,
                    'status' => 'DIBATALKAN',
                    'actor' => 'Admin ABS Cafe',
                    'description' => "Alasan: {$cancelReason}",
                    'logged_at' => now()->subHours(25 - $index),
                ]);
            }
        }

        // 6. Promos (3 promos)
        $catCoffee = Category::where('name', 'Coffee Base')->firstOrFail();

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

        // 7. Stock Logs
        StockLog::create([
            'menu_id' => 1,
            'item_name' => 'Caramel Latte (Bahan Sirup)',
            'type' => 'masuk',
            'quantity' => 30,
            'stock_before' => 15,
            'stock_after' => 45,
            'notes' => 'Restock sirup Monin Caramel & fresh milk Greenfields',
            'actor' => 'Admin ABS Cafe',
            'created_at' => now()->subHours(6),
        ]);

        StockLog::create([
            'menu_id' => 5,
            'item_name' => 'Aren Brown Sugar Coffee',
            'type' => 'keluar',
            'quantity' => 3,
            'stock_before' => 11,
            'stock_after' => 8,
            'notes' => 'Pengurangan otomatis dari pesanan meja',
            'actor' => 'Sistem Kasir',
            'created_at' => now()->subHours(2),
        ]);

        StockLog::create([
            'menu_id' => 14,
            'item_name' => 'ABS Canvas Tote Bag Natural',
            'type' => 'koreksi',
            'quantity' => -2,
            'stock_before' => 5,
            'stock_after' => 3,
            'notes' => 'Koreksi fisik: 2 pcs cacat cetak sablon disisihkan',
            'actor' => 'Admin ABS Cafe',
            'created_at' => now()->subDays(1),
        ]);

        // 8. Cafe Settings
        $settings = [
            'cafe_name' => 'ABS Cafe',
            'cafe_tagline' => 'Good Coffee, Better Moments.',
            'cafe_logo' => 'images/logo.png',
            'cafe_address' => 'Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219',
            'cafe_phone' => '0896-2010-2010',
            'cafe_email' => 'halo@abscafe.com',
            'cafe_hours' => 'Buka Setiap Hari 09.00 - 23.00 WIB',
            'cafe_maps_url' => 'https://maps.app.goo.gl/pPSXXjtJptYwrDB37',
            'peta_lat' => '-8.0646338',
            'peta_lng' => '111.9111798',
            'last_order' => '22.00 WIB',
            'cafe_instagram' => 'abs_tulungagung_24',
            'cafe_tiktok' => 'abs_tulungagung_24',
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
            'default_min_stock' => '10',
            'notif_low_stock' => '1',
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
            ['Menambah stok Caramel Latte sebanyak +30 porsi', 'stok', 'Admin Utama', 'admin', now()->subHours(6)],
            ['Koreksi inventaris fisik: ABS Canvas Tote Bag (-2 pcs rusak)', 'stok', 'Admin Utama', 'admin', now()->subDays(1)],
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
