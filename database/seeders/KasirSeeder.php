<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTimeline;
use App\Models\PaymentMethod;
use App\Models\Promo;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KasirSeeder extends Seeder
{
    /**
     * Data pendukung portal kasir: akun demo, voucher, metode bayar,
     * varian menu, contoh pesanan Web masuk, dan reservasi hari ini.
     */
    public function run(): void
    {
        // 1. Akun demo kasir
        User::updateOrCreate(
            ['email' => 'kasir@abscafe.com'],
            [
                'name' => 'Kasir Demo ABS Cafe',
                'username' => 'kasir',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'phone' => '081255667788',
                'status' => 'aktif',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&auto=format&fit=crop&q=80',
                'last_login_at' => now(),
            ]
        );

        // 2. Kode voucher pada promo yang berjalan
        $promoCodes = [
            'Buy 2 Get 1 Free All Coffee' => 'BELI2G1',
            'Diskon 20% Pelajar & Mahasiswa Tulungagung' => 'PELAJAR20',
            'Cashback Rp 10.000 Paket Ngemil Hemat' => 'NGEMIL10',
        ];
        foreach ($promoCodes as $title => $code) {
            Promo::where('title', $title)->update(['code' => $code]);
        }

        // 3. Metode pembayaran (apabila masih kosong)
        if (PaymentMethod::count() === 0) {
            $methods = [
                ['tunai', 'Tunai', 'cash', 'Pembayaran langsung dengan uang tunai.', 'Siapkan uang pas bila memungkinkan.', true, 1],
                ['qris', 'QRIS', 'e_wallet', 'Bayar scan QRIS melalui aplikasi pembayaran apa pun.', 'Arahkan kamera HP pelanggan ke kode QRIS kasir.', true, 2],
                ['debit', 'Kartu Debit / Kredit', 'debit', 'Pembayaran menggunakan kartu bank / EDC.', 'Gesek/tap kartu pada mesin EDC.', true, 3],
                ['transfer', 'Transfer Bank', 'bank_transfer', 'Transfer manual via m-banking.', 'Cocokkan nama & nominal transfer pelanggan.', true, 4],
                ['ewallet', 'E-Wallet', 'e_wallet', 'Pembayaran via e-wallet (OVO/GoPay/Dana).', 'Minta bukti transfer kepada pelanggan.', true, 5],
            ];

            foreach ($methods as [$code, $name, $type, $desc, $instr, $active, $sort]) {
                PaymentMethod::create([
                    'code' => $code,
                    'name' => $name,
                    'type' => $type,
                    'description' => $desc,
                    'instructions' => $instr,
                    'is_active' => $active,
                    'sort_order' => $sort,
                ]);
            }
        }

        // 4. Varian menu (apabila masih kosong) untuk ukuran Hot / Iced / Large
        if (MenuVariant::count() === 0) {
            $withVariants = [
                'Caramel Latte' => ['Hot' => 24000, 'Iced' => 26000, 'Large Iced' => 30000],
                'Cafe Latte' => ['Hot' => 22000, 'Iced' => 24000, 'Large Iced' => 28000],
                'Matcha Latte' => ['Hot' => 25000, 'Iced' => 27000, 'Large Iced' => 31000],
                'Signature Belgian Chocolate' => ['Hot' => 24000, 'Iced' => 26000],
                'Aren Brown Sugar Coffee' => ['Iced' => 20000, 'Large Iced' => 24000],
            ];

            foreach ($withVariants as $menuName => $variants) {
                $menu = Menu::where('name', $menuName)->first();
                if (! $menu) {
                    continue;
                }

                foreach ($variants as $variantName => $price) {
                    MenuVariant::create([
                        'menu_id' => $menu->id,
                        'name' => $variantName,
                        'price' => $price,
                        'is_default' => $variantName === 'Iced' || $variantName === 'Hot',
                    ]);
                }
            }
        }

        // 5. Contoh pesanan Web yang masuk hari ini (menunggu diterima)
        $kasir = User::where('email', 'siti.kasir@abscafe.com')->first() ?: User::where('email', 'kasir@abscafe.com')->first();
        $caramel = Menu::where('name', 'like', '%Caramel Latte%')->first() ?: Menu::first();
        $croissant = Menu::where('name', 'like', '%Churros%')->first() ?: Menu::first();
        $matcha = Menu::where('name', 'like', '%Matcha%')->first() ?: Menu::first();
        $fries = Menu::where('name', 'like', '%Kentang%')->first() ?: Menu::first();
        $nesmilo = Menu::where('name', 'like', '%Nasi Goreng ABS%')->first() ?: Menu::first();

        $webOrders = [
            [
                'code' => '#ORD-00001',
                'customer' => 'Intan Puspita',
                'type' => 'take_away',
                'items' => [
                    [$caramel, 1, 'Iced'],
                    [$croissant, 2, null],
                ],
                'minutes_ago' => 32,
            ],
            [
                'code' => '#ORD-00002',
                'customer' => 'Galih Pratama',
                'type' => 'dine_in',
                'table' => 5,
                'items' => [
                    [$matcha, 2, 'Iced'],
                    [$fries, 1, 'Extra saus keju'],
                ],
                'minutes_ago' => 18,
            ],
            [
                'code' => '#ORD-00003',
                'customer' => 'Risma Aulia',
                'type' => 'take_away',
                'items' => [
                    [$nesmilo, 1, null],
                    [$caramel, 1, 'Hot'],
                ],
                'minutes_ago' => 9,
            ],
        ];

        foreach ($webOrders as $seed) {
            $menuItems = collect($seed['items'])->filter(fn ($i) => $i[0])->values();
            if ($menuItems->isEmpty()) {
                continue;
            }

            $subtotal = 0;
            $lines = [];
            foreach ($menuItems as [$menu, $qty, $note]) {
                $price = (float) $menu->price;
                $name = $menu->name;
                if ($note === 'Iced' || $note === 'Hot' || $note === 'Large Iced') {
                    $variant = MenuVariant::where('menu_id', $menu->id)->where('name', $note)->first();
                    if ($variant) {
                        $price = (float) $variant->price;
                        $name = $menu->name.' ('.$variant->name.')';
                    }
                }
                $subtotal += $price * $qty;
                $lines[] = [$menu->id, $name, $price, $qty, $note === 'Iced' || $note === 'Hot' || $note === 'Large Iced' ? null : $note];
            }

            $tax = round($subtotal * 0.10, 2);
            $total = round($subtotal + $tax, 2);
            $created = now()->subMinutes($seed['minutes_ago']);

            $order = Order::updateOrCreate(
                ['order_code' => $seed['code']],
                [
                    'customer_name' => $seed['customer'],
                    'table_number' => $seed['table'] ?? null,
                    'order_type' => $seed['type'],
                    'source' => 'web',
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'service_charge' => 0,
                    'discount' => 0,
                    'total' => $total,
                    'payment_method' => null,
                    'payment_status' => 'menunggu_pembayaran',
                    'order_status' => 'baru',
                    'cashier_name' => null,
                    'paid_at' => null,
                    'received_at' => $created,
                    'created_at' => $created,
                    'updated_at' => $created,
                ]
            );

            $order->items()->delete();
            foreach ($lines as [$menuId, $name, $price, $qty, $note]) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menuId,
                    'name' => $name,
                    'price' => $price,
                    'quantity' => $qty,
                    'subtotal' => round($price * $qty, 2),
                    'notes' => $note,
                ]);
            }

            $order->timelines()->delete();
            OrderTimeline::create([
                'order_id' => $order->id,
                'status' => 'DIBUAT',
                'actor' => 'Sistem Web',
                'description' => 'Pesanan masuk dari platform Web, menunggu diterima kasir',
                'logged_at' => $created,
            ]);
        }

        // 6. Reservasi hari ini (contoh untuk halaman Reservasi & Meja)
        $tables = Table::where('status', '!=', 'nonaktif')->pluck('id', 'number');
        $today = today()->format('Y-m-d');
        $creator = $mayor->id ?? $kasir->id;

        $reservations = [
            ['Rahmat Hidayat', '085233445566', 4, $today, '10:30', $tables->get(3), 'menunggu', 'Titik temu teman kampus, siapkan meja dekat jendela.'],
            ['Laras Sari', '085712233445', 2, $today, '13:00', $tables->get(2), 'dikonfirmasi', null],
            ['Firdaus', '081377112233', 6, $today, '19:00', null, 'menunggu', 'Acara ulang tahun, minta dekorasi lilin kecil.'],
            ['Mentari', '081122334455', 2, $today, '20:30', $tables->get(5), 'datang', null],
        ];

        foreach ($reservations as $i => [$name, $phone, $size, $date, $time, $tableId, $status, $notes]) {
            Reservation::updateOrCreate(
                ['code' => 'RSV-'.now()->format('ymd').'-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)],
                [
                    'customer_name' => $name,
                    'phone' => $phone,
                    'party_size' => $size,
                    'reservation_date' => $date,
                    'reservation_time' => $time,
                    'table_id' => $tableId,
                    'status' => $status,
                    'notes' => $notes,
                    'created_by' => $creator,
                ]
            );

            if ($status === 'datang' && $tableId) {
                Table::whereKey($tableId)->update(['status' => 'terpakai']);
            }
        }
    }
}
