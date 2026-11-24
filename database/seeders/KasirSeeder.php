<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\PaymentMethod;
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
        // 1. Akun kasir ABS Cafe
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

        // 2. Metode pembayaran — hanya Tunai
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
    }
}
