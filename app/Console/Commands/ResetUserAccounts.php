<?php

namespace App\Console\Commands;

use App\Models\PenjagaKasir;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('app:reset-user-accounts')]
#[Description('Reset password dan akun admin (admin123), kasir (kasir123), dan owner (owner123)')]
class ResetUserAccounts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Mengatur akun login sementara:');

        // 1. Admin
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Utama ABS Cafe',
                'username' => 'admin',
                'email' => 'admin@abscafe.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'status' => 'aktif',
            ]
        );
        $this->line(" - Admin: username [admin] / password [admin123] (ID: {$admin->id})");

        // 2. Owner
        $owner = User::updateOrCreate(
            ['username' => 'owner'],
            [
                'name' => 'Owner ABS Cafe',
                'username' => 'owner',
                'email' => 'owner@abscafe.com',
                'password' => Hash::make('owner123'),
                'role' => 'owner',
                'phone' => '081100112233',
                'status' => 'aktif',
            ]
        );
        $this->line(" - Owner: username [owner] / password [owner123] (ID: {$owner->id})");

        // 3. Kasir (Hanya 1 akun login kasir terpusat)
        $kasir = User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir ABS Cafe',
                'username' => 'kasir',
                'email' => 'kasir@abscafe.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'phone' => '081255667788',
                'status' => 'aktif',
            ]
        );
        $this->line(" - Kasir: username [kasir] / password [kasir123] (ID: {$kasir->id})");

        // 4. Data Penjaga Kasir awal (nama random siap dipilih di Dashboard Kasir)
        $samplePenjaga = [
            ['name' => 'Siti Rahma', 'phone' => '082198765432'],
            ['name' => 'Budi Santoso', 'phone' => '081377889900'],
            ['name' => 'Rian Pratama', 'phone' => '081233445566'],
            ['name' => 'Dinda Ayu', 'phone' => '085677881122'],
            ['name' => 'Bagas Aditya', 'phone' => '087811223344'],
        ];

        foreach ($samplePenjaga as $p) {
            PenjagaKasir::firstOrCreate(
                ['name' => $p['name']],
                [
                    'phone' => $p['phone'],
                    'status' => 'aktif',
                ]
            );
            $this->line(" - Penjaga Kasir: {$p['name']} ({$p['phone']})");
        }

        // Pastikan akun kasir lama selain 'kasir' dipindahkan ke penjaga_kasir dan role diubah ke staf
        $otherKasirs = User::where('role', 'kasir')->where('username', '!=', 'kasir')->get();
        foreach ($otherKasirs as $oldKasir) {
            PenjagaKasir::firstOrCreate(
                ['name' => $oldKasir->name],
                [
                    'phone' => $oldKasir->phone,
                    'status' => 'aktif',
                ]
            );

            $oldKasir->update([
                'role' => 'staf',
                'status' => 'nonaktif',
            ]);
            $this->line("   * Mengonversi eks-kasir {$oldKasir->name} ({$oldKasir->username}) ke daftar Penjaga Kasir.");
        }

        $this->info('Berhasil! Semua akun siap digunakan.');

        return self::SUCCESS;
    }
}
