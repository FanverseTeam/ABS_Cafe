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
        Schema::table('pesanan', function (Blueprint $table) {
            if (! Schema::hasColumn('pesanan', 'customer_phone')) {
                $table->string('customer_phone', 30)->nullable()->after('customer_name');
            }
            if (! Schema::hasColumn('pesanan', 'customer_email')) {
                $table->string('customer_email', 100)->nullable()->after('customer_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('pesanan', 'customer_phone')) {
                $drop[] = 'customer_phone';
            }
            if (Schema::hasColumn('pesanan', 'customer_email')) {
                $drop[] = 'customer_email';
            }
            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
