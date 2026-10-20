<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('cafe_settings')
            ->where('key', 'landing_bg_url')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('cafe_settings')->insertOrIgnore([
            'key' => 'landing_bg_url',
            'value' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
