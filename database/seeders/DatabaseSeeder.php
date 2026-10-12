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
            ['email' => 'admin@abscafe.com'],
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
            ['email' => 'owner@abscafe.com'],
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
            ['email' => 'siti.kasir@abscafe.com'],
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
            ['email' => 'budi.kasir@abscafe.com'],
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

        // 2. Categories
        $catCoffee = Category::firstOrCreate(['name' => 'Coffee'], [
            'icon' => 'coffee',
            'order' => 1,
            'is_active' => true,
            'description' => 'Racikan kopi pilihan dari biji kopi arabika & robusta lokal berkualitas',
        ]);

        $catNonCoffee = Category::firstOrCreate(['name' => 'Non Coffee'], [
            'icon' => 'cup-soda',
            'order' => 2,
            'is_active' => true,
            'description' => 'Minuman segar, artisan tea, matcha, dan cokelat Belgia premium',
        ]);

        $catFood = Category::firstOrCreate(['name' => 'Food'], [
            'icon' => 'utensils',
            'order' => 3,
            'is_active' => true,
            'description' => 'Makanan berat, pastry hangat, dan camilan renyah pelengkap ngopi',
        ]);

        $catMerch = Category::firstOrCreate(['name' => 'Merch'], [
            'icon' => 'shopping-bag',
            'order' => 4,
            'is_active' => true,
            'description' => 'Aksesoris eksklusif ABS Cafe: tumbler, tote bag, dan whole beans',
        ]);

        // 3. Menus (15 items)
        $menus = [
            [
                'category_id' => $catCoffee->id,
                'name' => 'Caramel Latte',
                'price' => 24000,
                'description' => 'Espresso double shot dengan sirup karamel bakar dan susu segar creamy dengan foam lembut.',
                'image' => 'https://images.unsplash.com/photo-1599398054066-846f28917f38?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => true,
                'stock' => 45,
                'min_stock' => 15,
                'unit' => 'porsi',
                'sold_count' => 142,
            ],
            [
                'category_id' => $catCoffee->id,
                'name' => 'Cafe Latte',
                'price' => 22000,
                'description' => 'Espresso klasik seimbang dipadukan dengan steamed fresh milk beraroma harum.',
                'image' => 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => true,
                'stock' => 50,
                'min_stock' => 15,
                'unit' => 'porsi',
                'sold_count' => 188,
            ],
            [
                'category_id' => $catCoffee->id,
                'name' => 'Americano',
                'price' => 18000,
                'description' => 'Espresso murni diekstraksi sempurna dengan air mineral segar, citarasa bold dan fruity note.',
                'image' => 'https://images.unsplash.com/photo-1551030173-122aabc4489c?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => false,
                'stock' => 60,
                'min_stock' => 20,
                'unit' => 'porsi',
                'sold_count' => 95,
            ],
            [
                'category_id' => $catCoffee->id,
                'name' => 'Cappuccino',
                'price' => 23000,
                'description' => 'Rasio presisi antara espresso, steamed milk, dan busa susu tebal taburan bubuk coklat.',
                'image' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => true,
                'stock' => 38,
                'min_stock' => 10,
                'unit' => 'porsi',
                'sold_count' => 120,
            ],
            [
                'category_id' => $catCoffee->id,
                'name' => 'Aren Brown Sugar Coffee',
                'price' => 20000,
                'description' => 'Kopi susu khas Nusantara dengan manis legit gula aren murni asli Blitar/Tulungagung.',
                'image' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => true,
                'stock' => 8, // Menipis
                'min_stock' => 15,
                'unit' => 'porsi',
                'sold_count' => 210,
            ],
            [
                'category_id' => $catNonCoffee->id,
                'name' => 'Matcha Latte',
                'price' => 25000,
                'description' => 'Bubuk matcha Uji Jepang premium dipadukan dengan fresh milk lembut dan aroma earthy manis.',
                'image' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => true,
                'stock' => 22,
                'min_stock' => 10,
                'unit' => 'porsi',
                'sold_count' => 130,
            ],
            [
                'category_id' => $catNonCoffee->id,
                'name' => 'Signature Belgian Chocolate',
                'price' => 24000,
                'description' => 'Cokelat Belgia pekat 70% dilelehkan bersama susu segar hangat atau dingin.',
                'image' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => false,
                'stock' => 30,
                'min_stock' => 12,
                'unit' => 'porsi',
                'sold_count' => 78,
            ],
            [
                'category_id' => $catNonCoffee->id,
                'name' => 'Berry Sparkle Artisan Tea',
                'price' => 21000,
                'description' => 'Seduhan teh bunga telang & hibiscus dengan sirup stroberi segar dan soda menyegarkan.',
                'image' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => false,
                'stock' => 4, // Menipis
                'min_stock' => 10,
                'unit' => 'porsi',
                'sold_count' => 56,
            ],
            [
                'category_id' => $catFood->id,
                'name' => 'Butter Croissant',
                'price' => 18000,
                'description' => 'Pastry berlapis renyah luar lembut dalam dipanggang hangat dengan butter Prancis harum.',
                'image' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => true,
                'stock' => 14,
                'min_stock' => 10,
                'unit' => 'pcs',
                'sold_count' => 115,
            ],
            [
                'category_id' => $catFood->id,
                'name' => 'Nasi Goreng ABS Spesial',
                'price' => 28000,
                'description' => 'Nasi goreng racikan bumbu khas ABS Cafe disajikan dengan telur mata sapi, ayam suwir, dan kerupuk.',
                'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => true,
                'stock' => 25,
                'min_stock' => 10,
                'unit' => 'porsi',
                'sold_count' => 165,
            ],
            [
                'category_id' => $catFood->id,
                'name' => 'Kentang Goreng Truffle',
                'price' => 20000,
                'description' => 'Kentang stik renyah dibumbui minyak truffle aromatik, taburan peterseli, dan saus keju gurih.',
                'image' => 'https://images.unsplash.com/photo-1576107232684-1279f3908594?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => false,
                'stock' => 35,
                'min_stock' => 15,
                'unit' => 'porsi',
                'sold_count' => 98,
            ],
            [
                'category_id' => $catFood->id,
                'name' => 'Chicken Katsu Curry Rice',
                'price' => 32000,
                'description' => 'Fillet ayam krispi tebal dengan kuah kari Jepang gurih kental dan nasi pulen.',
                'image' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=500&auto=format&fit=crop&q=80',
                'is_available' => false,
                'is_best_seller' => false,
                'stock' => 0, // Habis
                'min_stock' => 8,
                'unit' => 'porsi',
                'sold_count' => 42,
            ],
            [
                'category_id' => $catMerch->id,
                'name' => 'ABS Tumbler Thermal 500ml',
                'price' => 85000,
                'description' => 'Tumbler stainless SUS 304 tahan dingin/panas 12 jam, grafir laser eksklusif logo ABS Cafe.',
                'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => true,
                'stock' => 18,
                'min_stock' => 5,
                'unit' => 'pcs',
                'sold_count' => 44,
            ],
            [
                'category_id' => $catMerch->id,
                'name' => 'ABS Canvas Tote Bag Natural',
                'price' => 45000,
                'description' => 'Tas kanvas tebal 12oz sablon line-art estetis ABS Cafe Boyolangu.',
                'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => false,
                'stock' => 3, // Menipis
                'min_stock' => 10,
                'unit' => 'pcs',
                'sold_count' => 28,
            ],
            [
                'category_id' => $catMerch->id,
                'name' => 'Biji Kopi House Blend 250g',
                'price' => 65000,
                'description' => 'Biji kopi sangrai fresh medium roast 70% Arabika Ijen & 30% Robusta Dampit, note nutty chocolatey.',
                'image' => 'https://images.unsplash.com/photo-1587734195503-904fca47e0e9?w=500&auto=format&fit=crop&q=80',
                'is_available' => true,
                'is_best_seller' => false,
                'stock' => 20,
                'min_stock' => 8,
                'unit' => 'pack',
                'sold_count' => 52,
            ],
        ];

        foreach ($menus as $m) {
            Menu::updateOrCreate(['name' => $m['name']], $m);
        }

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
        $menuList = Menu::all();

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
                $itemSubtotal = $menu->price * $qty;
                $subtotal += $itemSubtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
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
            'cafe_tagline' => 'Accessories & Cafe',
            'cafe_address' => 'Jl. Ki Mangunsarkoro No. 88, Beji, Kec. Boyolangu, Kabupaten Tulungagung, Jawa Timur 66233',
            'cafe_phone' => '0812-3456-7890',
            'cafe_email' => 'halo@abscafe.com',
            'tax_percent' => '10',
            'service_charge_active' => '0',
            'service_charge_percent' => '5',
            'payment_cash' => '1',
            'payment_qris' => '1',
            'payment_debit' => '1',
            'receipt_header' => "ABS CAFE - ACCESSORIES & CAFE\nJl. Ki Mangunsarkoro No. 88, Boyolangu, Tulungagung\nTelp: 0812-3456-7890",
            'receipt_footer' => "Terima kasih atas kunjungan Anda!\nFollow Instagram kami: @abscafe.tulungagung\n\"Good Coffee, Good Mood\"",
            'receipt_show_logo' => '1',
            'banner_title' => 'Nikmati Kopi Pilihan & Aksesoris Estetis',
            'banner_subtitle' => 'Tempat berkumpul hangat dengan aroma kopi terbaik di Boyolangu Tulungagung',
            'hours_weekday' => '09:00 - 22:00',
            'hours_weekend' => '09:00 - 23:30',
            // Pengaturan bisnis khusus Owner
            'target_daily_sales' => '2000000',
            'target_monthly_sales' => '45000000',
            'profit_margin_percent' => '40',
            'price_rounding' => 'none',
            'default_min_stock' => '10',
            'notif_low_stock' => '1',
            'notif_daily_report' => '1',
            'notif_weekly_report' => '0',
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
    }
}
