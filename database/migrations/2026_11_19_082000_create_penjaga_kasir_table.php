<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('penjaga_kasir')) {
            Schema::create('penjaga_kasir', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone', 30)->nullable();
                $table->string('status', 20)->default('aktif');
                $table->timestamps();
            });
        }

        // Masukkan data awal penjaga kasir jika masih kosong
        if (Schema::hasTable('penjaga_kasir') && DB::table('penjaga_kasir')->count() === 0) {
            DB::table('penjaga_kasir')->insert([
                [
                    'name' => 'Siti Rahma',
                    'phone' => '082198765432',
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Budi Santoso',
                    'phone' => '081377889900',
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Rian Pratama',
                    'phone' => '081233445566',
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Dinda Ayu',
                    'phone' => '085677881122',
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Bagas Aditya',
                    'phone' => '087811223344',
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // Pastikan akun login standar: admin (admin123), owner (owner123), kasir (kasir123)
        if (Schema::hasTable('pengguna')) {
            // 1. Admin
            $adminUser = DB::table('pengguna')->where('username', 'admin')->first();
            if (! $adminUser) {
                DB::table('pengguna')->insert([
                    'name' => 'Admin Utama ABS Cafe',
                    'username' => 'admin',
                    'email' => 'admin@abscafe.com',
                    'password' => Hash::make('admin123'),
                    'role' => 'admin',
                    'phone' => '081234567890',
                    'status' => 'aktif',
                    'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('pengguna')->where('id', $adminUser->id)->update([
                    'password' => Hash::make('admin123'),
                    'role' => 'admin',
                    'status' => 'aktif',
                ]);
            }

            // 2. Owner
            $ownerUser = DB::table('pengguna')->where('username', 'owner')->first();
            if (! $ownerUser) {
                DB::table('pengguna')->insert([
                    'name' => 'Owner ABS Cafe',
                    'username' => 'owner',
                    'email' => 'owner@abscafe.com',
                    'password' => Hash::make('owner123'),
                    'role' => 'owner',
                    'phone' => '081100112233',
                    'status' => 'aktif',
                    'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&auto=format&fit=crop&q=80',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('pengguna')->where('id', $ownerUser->id)->update([
                    'password' => Hash::make('owner123'),
                    'role' => 'owner',
                    'status' => 'aktif',
                ]);
            }

            // 3. Kasir (Hanya 1 akun login kasir terpusat)
            $kasirUser = DB::table('pengguna')->where('username', 'kasir')->first();
            if (! $kasirUser) {
                DB::table('pengguna')->insert([
                    'name' => 'Kasir ABS Cafe',
                    'username' => 'kasir',
                    'email' => 'kasir@abscafe.com',
                    'password' => Hash::make('kasir123'),
                    'role' => 'kasir',
                    'phone' => '081255667788',
                    'status' => 'aktif',
                    'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&auto=format&fit=crop&q=80',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('pengguna')->where('id', $kasirUser->id)->update([
                    'password' => Hash::make('kasir123'),
                    'role' => 'kasir',
                    'status' => 'aktif',
                ]);
            }

            // Ubah akun login kasir lain yang bukan 'kasir' agar tidak ada duplikasi akun login kasir
            $otherKasir = DB::table('pengguna')->where('role', 'kasir')->where('username', '!=', 'kasir')->get();
            foreach ($otherKasir as $user) {
                // Pastikan nama penjaga kasir sudah masuk ke tabel penjaga_kasir
                $exists = DB::table('penjaga_kasir')->where('name', $user->name)->exists();
                if (! $exists) {
                    DB::table('penjaga_kasir')->insert([
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'status' => 'aktif',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Ubah role menjadi staf agar login kasir hanya 1 akun
                DB::table('pengguna')->where('id', $user->id)->update([
                    'role' => 'staf',
                    'status' => 'nonaktif',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjaga_kasir');
    }
};
