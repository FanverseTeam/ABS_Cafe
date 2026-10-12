<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Melengkapi modul transaksi agar siap dipakai role Kasir (POS, pesanan masuk,
     * buka/tutup shift) tanpa perlu migrasi ulang di tahap berikutnya.
     *
     * Catatan: tabel `orders` dan `order_items` sudah ada (dibuat migrasi 000001),
     * sehingga di sini hanya menambah kolom relasi yang belum ada.
     */
    public function up(): void
    {
        // 1. Shift kasir (buka/tutup kas)
        Schema::create('cash_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('opening_cash', 12, 2)->default(0);
            $table->decimal('expected_cash', 12, 2)->default(0);
            $table->decimal('closing_cash', 12, 2)->default(0);
            $table->decimal('cash_difference', 12, 2)->default(0);
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->unsignedInteger('total_transactions')->default(0);
            $table->string('status', 20)->default('buka'); // buka, tutup
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'opened_at']);
        });

        // 2. Kolom relasi pada orders (aman bila sudah ada)
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'cashier_id')) {
                $table->foreignId('cashier_id')->nullable()->after('cashier_name')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'promo_id')) {
                $table->foreignId('promo_id')->nullable()->after('discount')
                    ->constrained('promos')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'cash_shift_id')) {
                $table->foreignId('cash_shift_id')->nullable()->after('promo_id')
                    ->constrained('cash_shifts')->nullOnDelete();
            }
        });

        // 3. Kuota promo (opsional) untuk kampanye berbatas jumlah
        Schema::table('promos', function (Blueprint $table) {
            if (!Schema::hasColumn('promos', 'quota')) {
                $table->unsignedInteger('quota')->nullable()->after('min_spend');
            }
        });

        // 4. Pembayaran (mendukung split/catatan pembayaran per order)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('method', 30)->default('tunai'); // tunai, qris, debit
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('cash_received', 12, 2)->default(0);
            $table->decimal('change_given', 12, 2)->default(0);
            $table->string('reference')->nullable();
            $table->string('status', 30)->default('lunas'); // lunas, menunggu, gagal
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->index(['method', 'paid_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'cash_shift_id')) {
                $table->dropConstrainedForeignId('cash_shift_id');
            }
            if (Schema::hasColumn('orders', 'promo_id')) {
                $table->dropConstrainedForeignId('promo_id');
            }
            if (Schema::hasColumn('orders', 'cashier_id')) {
                $table->dropConstrainedForeignId('cashier_id');
            }
        });

        Schema::dropIfExists('cash_shifts');
    }
};
