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
        // Update users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
            $table->string('role')->default('staf')->after('email'); // admin, kasir, staf
            $table->string('phone')->nullable()->after('role');
            $table->string('status')->default('aktif')->after('phone'); // aktif, nonaktif
            $table->string('avatar')->nullable()->after('status');
            $table->timestamp('last_login_at')->nullable()->after('avatar');
        });

        // Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->default('Coffee');
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Menus
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_best_seller')->default(false);
            $table->integer('stock')->default(50);
            $table->integer('min_stock')->default(10);
            $table->string('unit')->default('porsi'); // porsi, cup, pcs, pack
            $table->integer('sold_count')->default(0);
            $table->timestamps();
        });

        // Tables (Meja)
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->unique();
            $table->integer('capacity')->default(4);
            $table->string('status')->default('tersedia'); // tersedia, terpakai, nonaktif
            $table->string('location_notes')->nullable();
            $table->timestamps();
        });

        // Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // e.g. #AB261001
            $table->string('customer_name');
            $table->integer('table_number')->nullable();
            $table->string('order_type')->default('dine_in'); // dine_in, take_away
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('service_charge', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('payment_method')->default('qris'); // tunai, qris, debit
            $table->string('payment_status')->default('menunggu_pembayaran'); // menunggu_pembayaran, lunas, dibatalkan
            $table->string('order_status')->default('diproses'); // diproses, siap, selesai
            $table->string('cashier_name')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
        });

        // Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('menu_id')->nullable()->constrained('menus')->onDelete('set null');
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // Order Timelines
        Schema::create('order_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('status');
            $table->string('actor')->nullable();
            $table->text('description');
            $table->timestamp('logged_at')->useCurrent();
        });

        // Stock Logs
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->nullable()->constrained('menus')->onDelete('set null');
            $table->string('item_name');
            $table->string('type'); // masuk, keluar, koreksi
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('notes')->nullable();
            $table->string('actor')->default('Admin');
            $table->timestamps();
        });

        // Promos
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('type'); // persen, nominal, buy_x_get_y
            $table->decimal('value', 10, 2);
            $table->string('applies_to')->default('semua'); // semua, kategori, menu
            $table->foreignId('target_category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('banner')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('min_spend', 12, 2)->default(0);
            $table->timestamps();
        });

        // Cafe Settings
        Schema::create('cafe_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->string('role')->default('admin');
            $table->text('activity');
            $table->string('activity_type')->default('sistem'); // menu, pesanan, stok, pengguna, promo, meja, sistem
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('cafe_settings');
        Schema::dropIfExists('promos');
        Schema::dropIfExists('stock_logs');
        Schema::dropIfExists('order_timelines');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('categories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'phone', 'status', 'avatar', 'last_login_at']);
        });
    }
};
