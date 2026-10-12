<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Skema tambahan untuk modul Kasir: sumber pesanan (Web/Offline) pada orders,
     * catatan kas masuk/keluar shift, dan reservasi meja.
     */
    public function up(): void
    {
        // 1. Sumber & waktu terima pesanan (web vs offline)
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'source')) {
                $table->string('source', 20)->default('offline')->after('order_type'); // offline, web
            }
            if (! Schema::hasColumn('orders', 'received_at')) {
                $table->dateTime('received_at')->nullable()->after('paid_at');
            }
        });

        // 1b. Kode voucher pada promo (opsional) agar bisa divalidasi kasir
        Schema::table('promos', function (Blueprint $table) {
            if (! Schema::hasColumn('promos', 'code')) {
                $table->string('code')->nullable()->unique()->after('title');
            }
        });

        // 1c. Varian menu yang dipilih pada tiap baris pesanan
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('menu_id');
            }
        });

        // 2. Kas masuk/keluar manual selama shift berjalan
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_shift_id')->constrained('cash_shifts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 10)->default('keluar'); // masuk, keluar
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('reason');
            $table->timestamps();

            $table->index(['cash_shift_id', 'type']);
        });

        // 3. Reservasi meja
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('customer_name');
            $table->string('phone')->nullable();
            $table->unsignedInteger('party_size')->default(2);
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
            $table->string('status', 20)->default('menunggu'); // menunggu, dikonfirmasi, datang, selesai, batal, tidak_datang
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['reservation_date', 'reservation_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('cash_movements');

        Schema::table('promos', function (Blueprint $table) {
            if (Schema::hasColumn('promos', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'variant_id')) {
                $table->dropColumn('variant_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'received_at')) {
                $table->dropColumn('received_at');
            }
            if (Schema::hasColumn('orders', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
