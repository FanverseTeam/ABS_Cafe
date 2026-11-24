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
        Schema::table('penjaga_kasir', function (Blueprint $table) {
            if (! Schema::hasColumn('penjaga_kasir', 'bagian')) {
                $table->string('bagian', 30)->default('kasir')->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjaga_kasir', function (Blueprint $table) {
            if (Schema::hasColumn('penjaga_kasir', 'bagian')) {
                $table->dropColumn('bagian');
            }
        });
    }
};
