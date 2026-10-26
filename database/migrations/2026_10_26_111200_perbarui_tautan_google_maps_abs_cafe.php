<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Memperbarui cafe_maps_url ke tautan Google Maps resmi ABS Cafe yang terbaru.
     * Hanya menimpa nilai yang masih berisi link lama — tidak menimpa nilai
     * yang sudah diubah admin melalui Pengaturan Website.
     */
    public function up(): void
    {
        $table = Schema::hasTable('pengaturan') ? 'pengaturan' : (Schema::hasTable('cafe_settings') ? 'cafe_settings' : null);

        if (! $table) {
            return;
        }

        $now = now();

        /**
         * Link lama yang boleh ditimpa.
         * Link apa pun di luar daftar ini dianggap sudah diubah admin → dilewati.
         */
        $oldMapsValues = [
            'https://maps.app.goo.gl/pPSXXjtJptYwrDB37',
            'https://www.google.com/maps/search/?api=1&query=-8.0646285%2C111.9086049',
            'https://www.google.com/maps/search/?api=1&query=-8.0646285,111.9086049',
            'https://maps.google.com/?q=Jl.+Melati+No.+59+Tulungagung',
            'https://maps.google.com/?q=ABS+Cafe+Tulungagung',
            '',
        ];

        $newMaps = 'https://maps.app.goo.gl/mvEr6sWCacKH5j677';

        $row = DB::table($table)->where('key', 'cafe_maps_url')->first();

        if ($row) {
            if (in_array(trim((string) ($row->value ?? '')), $oldMapsValues, true)) {
                DB::table($table)
                    ->where('key', 'cafe_maps_url')
                    ->update(['value' => $newMaps, 'updated_at' => $now]);
            }
            // Jika nilai sudah berbeda dari semua link lama, berarti admin sudah mengubahnya → lewati.
        } else {
            DB::table($table)->insert([
                'key' => 'cafe_maps_url',
                'value' => $newMaps,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Migrasi data tidak membatalkan perubahan nilai — tidak ada rollback destruktif.
     */
    public function down(): void
    {
        // Migrasi data tidak membatalkan pembaruan tautan peta.
    }
};
