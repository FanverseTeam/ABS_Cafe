<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skema live (migrasi 000001) sudah memiliki `phone` dan `last_login_at`.
        // Tambahkan hanya kolom yang belum ada agar aman dijalankan di database yang sudah terisi.
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'photo_path')) {
                $table->dropColumn('photo_path');
            }
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
