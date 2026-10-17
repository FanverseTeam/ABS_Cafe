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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('menu_variant_id')->nullable()->constrained('menu_variants')->cascadeOnDelete();
            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(1.00);
            $table->timestamps();

            $table->index(['menu_id', 'stock_id']);
            $table->index(['menu_id', 'menu_variant_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'stock_deducted')) {
                $table->boolean('stock_deducted')->default(false)->after('payment_status');
            }
            if (! Schema::hasColumn('orders', 'stock_returned')) {
                $table->boolean('stock_returned')->default(false)->after('stock_deducted');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'stock_returned')) {
                $table->dropColumn('stock_returned');
            }
            if (Schema::hasColumn('orders', 'stock_deducted')) {
                $table->dropColumn('stock_deducted');
            }
        });

        Schema::dropIfExists('recipes');
    }
};
