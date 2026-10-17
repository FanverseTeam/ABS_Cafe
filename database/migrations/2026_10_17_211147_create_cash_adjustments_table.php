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
        Schema::create('cash_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('reason');
            $table->foreignId('applied_to_shift_id')->nullable()->constrained('cash_shifts')->nullOnDelete();
            $table->timestamps();

            $table->index(['applied_to_shift_id', 'created_at']);
        });

        Schema::table('cash_shifts', function (Blueprint $table) {
            if (! Schema::hasColumn('cash_shifts', 'opening_cash_source')) {
                $table->string('opening_cash_source')->nullable()->after('opening_cash');
            }
            if (! Schema::hasColumn('cash_shifts', 'is_auto_closed')) {
                $table->boolean('is_auto_closed')->default(true)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_shifts', function (Blueprint $table) {
            if (Schema::hasColumn('cash_shifts', 'is_auto_closed')) {
                $table->dropColumn('is_auto_closed');
            }
            if (Schema::hasColumn('cash_shifts', 'opening_cash_source')) {
                $table->dropColumn('opening_cash_source');
            }
        });

        Schema::dropIfExists('cash_adjustments');
    }
};
