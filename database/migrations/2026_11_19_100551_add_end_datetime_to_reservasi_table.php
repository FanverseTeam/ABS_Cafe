<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->date('reservation_end_date')->nullable()->after('reservation_time');
            $table->time('reservation_end_time')->nullable()->after('reservation_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropColumn(['reservation_end_date', 'reservation_end_time']);
        });
    }
};
