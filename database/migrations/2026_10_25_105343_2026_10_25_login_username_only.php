<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ubah kolom pengguna untuk login berbasis username saja.
 *
 * - username: isi dari email jika NULL, jadikan NOT NULL UNIQUE
 * - email: jadikan nullable, hapus UNIQUE constraint
 * - Hapus tabel password_reset_tokens (tidak dipakai lagi)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Isi username dari bagian sebelum "@" pada email untuk baris yang belum punya username
        $users = DB::table('pengguna')->whereNull('username')->orWhere('username', '')->get(['id', 'email']);

        foreach ($users as $user) {
            $base = strtolower(explode('@', $user->email)[0]);
            $base = preg_replace('/[^a-z0-9._]/', '_', $base);
            $candidate = $base;
            $counter = 1;

            while (DB::table('pengguna')->where('username', $candidate)->where('id', '!=', $user->id)->exists()) {
                $candidate = $base.'_'.$counter;
                $counter++;
            }

            DB::table('pengguna')->where('id', $user->id)->update(['username' => $candidate]);
        }

        Schema::table('pengguna', function (Blueprint $table) {
            // Hapus unique constraint email terlebih dahulu
            $table->dropUnique('pengguna_email_unique');

            // Jadikan email nullable (tidak dihapus, hanya diizinkan kosong)
            $table->string('email')->nullable()->change();

            // username menjadi NOT NULL dan UNIQUE
            $table->string('username')->nullable(false)->unique()->change();
        });

        // Hapus tabel password_reset_tokens karena tidak dipakai lagi (login pakai username saja)
        if (Schema::hasTable('password_reset_tokens')) {
            Schema::drop('password_reset_tokens');
        }
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropUnique('pengguna_username_unique');
            $table->string('username')->nullable()->change();
            $table->string('email')->nullable(false)->change();
            $table->unique('email', 'pengguna_email_unique');
        });

        // Re-create password_reset_tokens table on rollback
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }
};
