<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table = Schema::hasTable('pengaturan') ? 'pengaturan' : (Schema::hasTable('cafe_settings') ? 'cafe_settings' : null);

        if (! $table) {
            return;
        }

        $now = now();

        $oldLatValues = [
            '-8.0646285',
            '-8.06462',
            '',
        ];

        $oldLngValues = [
            '111.9086049',
            '111.9086',
            '',
        ];

        $oldMapsValues = [
            'https://www.google.com/maps/search/?api=1&query=-8.0646285%2C111.9086049',
            'https://www.google.com/maps/search/?api=1&query=-8.0646285,111.9086049',
            'https://maps.google.com/?q=Jl.+Melati+No.+59+Tulungagung',
            'https://maps.google.com/?q=ABS+Cafe+Tulungagung',
            '',
        ];

        $correctLat = '-8.0646338';
        $correctLng = '111.9111798';
        $correctMaps = 'https://maps.app.goo.gl/pPSXXjtJptYwrDB37';

        // 1. Koreksi peta_lat bila masih nilai lama yang meleset
        $latRow = DB::table($table)->where('key', 'peta_lat')->first();
        if ($latRow) {
            if (in_array(trim((string) ($latRow->value ?? '')), $oldLatValues, true)) {
                DB::table($table)->where('key', 'peta_lat')->update(['value' => $correctLat, 'updated_at' => $now]);
            }
        } else {
            DB::table($table)->insert(['key' => 'peta_lat', 'value' => $correctLat, 'created_at' => $now, 'updated_at' => $now]);
        }

        // 2. Koreksi peta_lng bila masih nilai lama yang meleset
        $lngRow = DB::table($table)->where('key', 'peta_lng')->first();
        if ($lngRow) {
            if (in_array(trim((string) ($lngRow->value ?? '')), $oldLngValues, true)) {
                DB::table($table)->where('key', 'peta_lng')->update(['value' => $correctLng, 'updated_at' => $now]);
            }
        } else {
            DB::table($table)->insert(['key' => 'peta_lng', 'value' => $correctLng, 'created_at' => $now, 'updated_at' => $now]);
        }

        // 3. Koreksi cafe_maps_url bila masih nilai lama
        $mapsRow = DB::table($table)->where('key', 'cafe_maps_url')->first();
        if ($mapsRow) {
            if (in_array(trim((string) ($mapsRow->value ?? '')), $oldMapsValues, true)) {
                DB::table($table)->where('key', 'cafe_maps_url')->update(['value' => $correctMaps, 'updated_at' => $now]);
            }
        } else {
            DB::table($table)->insert(['key' => 'cafe_maps_url', 'value' => $correctMaps, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migrasi data tidak membatalkan perbaikan koordinat
    }
};
