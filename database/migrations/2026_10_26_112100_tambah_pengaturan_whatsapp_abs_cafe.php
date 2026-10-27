<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan pengaturan whatsapp_nomor dan whatsapp_pesan jika belum ada.
     * Tidak menimpa nilai yang sudah diubah admin.
     */
    public function up(): void
    {
        $table = Schema::hasTable('pengaturan') ? 'pengaturan' : (Schema::hasTable('cafe_settings') ? 'cafe_settings' : null);

        if (! $table) {
            return;
        }

        $now = now();

        if (! DB::table($table)->where('key', 'whatsapp_nomor')->exists()) {
            DB::table($table)->insert([
                'key' => 'whatsapp_nomor',
                'value' => '6289620102010',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (! DB::table($table)->where('key', 'whatsapp_pesan')->exists()) {
            DB::table($table)->insert([
                'key' => 'whatsapp_pesan',
                'value' => 'Halo ABS Cafe, saya ingin bertanya.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Migrasi data tidak membatalkan penambahan key — tidak ada rollback destruktif.
     */
    public function down(): void
    {
        // Migrasi data tidak membatalkan penambahan pengaturan WhatsApp.
    }
};
