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

        $oldAddressValues = [
            'Jl. Melati No. 59, Tulungagung, Jawa Timur',
            'Jl. Melati No. 59',
            'Jl. Ki Mangunsarkoro No. 88, Boyolangu, Tulungagung',
            'Jl. Letjen Suprapto No.44',
            'Jl. Letjen Suprapto No. 44',
            '',
        ];

        $oldPhoneValues = [
            '0812 3456 7890',
            '0812-3456-7890',
            '+62 812 3456 7890',
            '+6281234567890',
            '081234567890',
            '',
        ];

        $oldMapsValues = [
            'https://maps.google.com/?q=Jl.+Melati+No.+59+Tulungagung',
            'https://maps.google.com/?q=ABS+Cafe+Tulungagung',
            '',
        ];

        $oldHoursValues = [
            'Buka Setiap Hari 08.00 - 22.00 WIB',
            '08.00 - 22.00',
            '08:00 - 22:00',
            '',
        ];

        $newAddress = 'Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219';
        $newPhone = '0896-2010-2010';
        $newMaps = 'https://maps.app.goo.gl/pPSXXjtJptYwrDB37';
        $newHours = 'Buka Setiap Hari 09.00 - 23.00 WIB';
        $newWeekdayHours = '09:00 - 23:00';
        $newWeekendHours = '09:00 - 23:00';
        $newReceiptHeader = "ABS CAFE - ACCESSORIES & CAFE\nJl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219\nTelp: 0896-2010-2010";

        // 1. Update cafe_address jika masih placeholder lama
        $addrSetting = DB::table($table)->where('key', 'cafe_address')->first();
        if ($addrSetting) {
            if (in_array(trim($addrSetting->value ?? ''), $oldAddressValues, true)) {
                DB::table($table)->where('key', 'cafe_address')->update(['value' => $newAddress, 'updated_at' => $now]);
            }
        } else {
            DB::table($table)->insert(['key' => 'cafe_address', 'value' => $newAddress, 'created_at' => $now, 'updated_at' => $now]);
        }

        // 2. Update cafe_phone jika masih placeholder lama
        $phoneSetting = DB::table($table)->where('key', 'cafe_phone')->first();
        if ($phoneSetting) {
            if (in_array(trim($phoneSetting->value ?? ''), $oldPhoneValues, true)) {
                DB::table($table)->where('key', 'cafe_phone')->update(['value' => $newPhone, 'updated_at' => $now]);
            }
        } else {
            DB::table($table)->insert(['key' => 'cafe_phone', 'value' => $newPhone, 'created_at' => $now, 'updated_at' => $now]);
        }

        // 3. Update cafe_maps_url jika masih placeholder lama
        $mapsSetting = DB::table($table)->where('key', 'cafe_maps_url')->first();
        if ($mapsSetting) {
            if (in_array(trim($mapsSetting->value ?? ''), $oldMapsValues, true)) {
                DB::table($table)->where('key', 'cafe_maps_url')->update(['value' => $newMaps, 'updated_at' => $now]);
            }
        } else {
            DB::table($table)->insert(['key' => 'cafe_maps_url', 'value' => $newMaps, 'created_at' => $now, 'updated_at' => $now]);
        }

        // 4. Update cafe_hours jika masih placeholder lama
        $hoursSetting = DB::table($table)->where('key', 'cafe_hours')->first();
        if ($hoursSetting) {
            if (in_array(trim($hoursSetting->value ?? ''), $oldHoursValues, true)) {
                DB::table($table)->where('key', 'cafe_hours')->update(['value' => $newHours, 'updated_at' => $now]);
            }
        } else {
            DB::table($table)->insert(['key' => 'cafe_hours', 'value' => $newHours, 'created_at' => $now, 'updated_at' => $now]);
        }

        // 5. Update hours_weekday & hours_weekend jika masih placeholder lama
        $weekdaySetting = DB::table($table)->where('key', 'hours_weekday')->first();
        if ($weekdaySetting) {
            if (in_array(trim($weekdaySetting->value ?? ''), ['08:00 - 22:00', '09:00 - 22:00', ''], true)) {
                DB::table($table)->where('key', 'hours_weekday')->update(['value' => $newWeekdayHours, 'updated_at' => $now]);
            }
        } else {
            DB::table($table)->insert(['key' => 'hours_weekday', 'value' => $newWeekdayHours, 'created_at' => $now, 'updated_at' => $now]);
        }

        $weekendSetting = DB::table($table)->where('key', 'hours_weekend')->first();
        if ($weekendSetting) {
            if (in_array(trim($weekendSetting->value ?? ''), ['08:00 - 22:00', '08:00 - 23:00', '09:00 - 23:30', ''], true)) {
                DB::table($table)->where('key', 'hours_weekend')->update(['value' => $newWeekendHours, 'updated_at' => $now]);
            }
        } else {
            DB::table($table)->insert(['key' => 'hours_weekend', 'value' => $newWeekendHours, 'created_at' => $now, 'updated_at' => $now]);
        }

        // 6. Update receipt_header jika mengandung placeholder lama
        $receiptSetting = DB::table($table)->where('key', 'receipt_header')->first();
        if ($receiptSetting) {
            $val = $receiptSetting->value ?? '';
            if (str_contains($val, 'Jl. Melati') || str_contains($val, 'Ki Mangunsarkoro') || str_contains($val, '0812 3456') || str_contains($val, '0812-3456')) {
                DB::table($table)->where('key', 'receipt_header')->update(['value' => $newReceiptHeader, 'updated_at' => $now]);
            }
        } else {
            DB::table($table)->insert(['key' => 'receipt_header', 'value' => $newReceiptHeader, 'created_at' => $now, 'updated_at' => $now]);
        }

        // 7. Tambah key baru peta_lat jika belum ada
        if (! DB::table($table)->where('key', 'peta_lat')->exists()) {
            DB::table($table)->insert(['key' => 'peta_lat', 'value' => '-8.0646338', 'created_at' => $now, 'updated_at' => $now]);
        }

        // 8. Tambah key baru peta_lng jika belum ada
        if (! DB::table($table)->where('key', 'peta_lng')->exists()) {
            DB::table($table)->insert(['key' => 'peta_lng', 'value' => '111.9111798', 'created_at' => $now, 'updated_at' => $now]);
        }

        // 9. Tambah key baru last_order jika belum ada
        if (! DB::table($table)->where('key', 'last_order')->exists()) {
            DB::table($table)->insert(['key' => 'last_order', 'value' => '22.00 WIB', 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migrasi data tidak membatalkan nilai baru
    }
};
