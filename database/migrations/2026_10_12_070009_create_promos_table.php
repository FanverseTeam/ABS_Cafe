<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel `promos` sudah dibuat oleh migrasi 000001 (skema live).
        if (Schema::hasTable('promos')) {
            return;
        }

        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->enum('type', ['persen', 'nominal', 'beli_2_gratis_1'])->default('persen');
            $table->decimal('value', 12, 2)->default(0);
            $table->decimal('min_purchase', 12, 2)->default(0);
            $table->string('applicable_scope', 20)->default('semua');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->unsignedInteger('quota')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->string('banner_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Tidak menghapus `promos` milik skema live.
    }
};
