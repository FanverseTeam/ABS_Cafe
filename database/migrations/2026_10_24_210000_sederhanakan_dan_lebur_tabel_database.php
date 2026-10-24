<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // 1. PENGGUNA (users)
        if (! Schema::hasTable('pengguna')) {
            Schema::create('pengguna', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('username')->nullable();
                $table->string('email')->unique();
                $table->string('role')->default('kasir');
                $table->string('phone')->nullable();
                $table->string('photo_path')->nullable();
                $table->string('status')->default('aktif');
                $table->string('avatar')->nullable();
                $table->timestamp('last_login_at')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. KATEGORI (categories)
        if (! Schema::hasTable('kategori')) {
            Schema::create('kategori', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('icon')->default('coffee');
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. MENU (menus)
        $createdMenu = false;
        if (! Schema::hasTable('menu')) {
            $createdMenu = true;
            Schema::create('menu', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('merged_into_menu_id')->nullable();
                $table->unsignedBigInteger('variant_id')->nullable();
                $table->unsignedBigInteger('category_id');
                $table->string('name');
                $table->decimal('price', 12, 2)->default(0.00);
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_available')->default(true);
                $table->boolean('is_best_seller')->default(false);
                $table->integer('stock')->default(0);
                $table->integer('min_stock')->default(5);
                $table->string('unit')->default('porsi');
                $table->integer('sold_count')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('category_id')->references('id')->on('kategori')->onDelete('cascade');
                $table->foreign('merged_into_menu_id')->references('id')->on('menu')->onDelete('set null');
            });
        }

        // 4. MENU_VARIAN (menu_variants)
        if (! Schema::hasTable('menu_varian')) {
            Schema::create('menu_varian', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('menu_id');
                $table->string('name');
                $table->string('size')->nullable();
                $table->decimal('price', 12, 2)->default(0.00);
                $table->boolean('is_available')->default(true);
                $table->boolean('is_default')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('menu_id')->references('id')->on('menu')->onDelete('cascade');
            });
        }

        // Foreign key menu ke menu_varian
        if ($createdMenu && Schema::hasTable('menu_varian')) {
            Schema::table('menu', function (Blueprint $table) {
                $table->foreign('variant_id')->references('id')->on('menu_varian')->onDelete('set null');
            });
        }

        // 5. FAVORIT (favorites)
        if (! Schema::hasTable('favorit')) {
            Schema::create('favorit', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('menu_id');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('pengguna')->onDelete('cascade');
                $table->foreign('menu_id')->references('id')->on('menu')->onDelete('cascade');
            });
        }

        // 6. MEJA (lebur tables + cafe_tables)
        if (! Schema::hasTable('meja')) {
            Schema::create('meja', function (Blueprint $table) {
                $table->id();
                $table->integer('number');
                $table->integer('capacity')->default(2);
                $table->string('status')->default('tersedia');
                $table->string('location_notes')->nullable();
                $table->string('area', 50)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 7. METODE_BAYAR (payment_methods)
        if (! Schema::hasTable('metode_bayar')) {
            Schema::create('metode_bayar', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 50)->unique();
                $table->string('type', 30);
                $table->text('description')->nullable();
                $table->text('instructions')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 8. PROMO (promos)
        if (! Schema::hasTable('promo')) {
            Schema::create('promo', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('code')->nullable()->unique();
                $table->text('description');
                $table->string('type')->default('persen');
                $table->decimal('value', 10, 2);
                $table->string('applies_to')->default('semua');
                $table->unsignedBigInteger('target_category_id')->nullable();
                $table->unsignedBigInteger('target_menu_id')->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->string('banner')->nullable();
                $table->boolean('is_active')->default(true);
                $table->decimal('min_spend', 12, 2)->default(0.00);
                $table->unsignedInteger('quota')->nullable();
                $table->timestamps();

                $table->foreign('target_category_id')->references('id')->on('kategori')->onDelete('set null');
                $table->foreign('target_menu_id')->references('id')->on('menu')->onDelete('set null');
            });
        }

        // 9. PENGATURAN (lebur cafe_settings + settings)
        if (! Schema::hasTable('pengaturan')) {
            Schema::create('pengaturan', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('group', 50)->nullable();
                $table->timestamps();
            });
        }

        // 10. STOK (stocks)
        if (! Schema::hasTable('stok')) {
            Schema::create('stok', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('unit', 30);
                $table->decimal('quantity', 12, 2)->default(0.00);
                $table->decimal('min_quantity', 12, 2)->default(0.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 11. PEMASOK (suppliers)
        if (! Schema::hasTable('pemasok')) {
            Schema::create('pemasok', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 12. STOK_PEMASOK (stock_supplier)
        if (! Schema::hasTable('stok_pemasok')) {
            Schema::create('stok_pemasok', function (Blueprint $table) {
                $table->unsignedBigInteger('stock_id');
                $table->unsignedBigInteger('supplier_id');
                $table->timestamps();

                $table->primary(['stock_id', 'supplier_id']);
                $table->foreign('stock_id')->references('id')->on('stok')->onDelete('cascade');
                $table->foreign('supplier_id')->references('id')->on('pemasok')->onDelete('cascade');
            });
        }

        // 13. RESEP (recipes)
        if (! Schema::hasTable('resep')) {
            Schema::create('resep', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('menu_id');
                $table->unsignedBigInteger('menu_variant_id')->nullable();
                $table->unsignedBigInteger('stock_id');
                $table->decimal('amount', 12, 2);
                $table->timestamps();

                $table->foreign('menu_id')->references('id')->on('menu')->onDelete('cascade');
                $table->foreign('menu_variant_id')->references('id')->on('menu_varian')->onDelete('cascade');
                $table->foreign('stock_id')->references('id')->on('stok')->onDelete('cascade');
            });
        }

        // 14. SHIFT (cash_shifts)
        if (! Schema::hasTable('shift')) {
            Schema::create('shift', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->datetime('opened_at');
                $table->datetime('closed_at')->nullable();
                $table->decimal('opening_cash', 12, 2)->default(0.00);
                $table->string('opening_cash_source')->nullable();
                $table->decimal('expected_cash', 12, 2)->default(0.00);
                $table->decimal('closing_cash', 12, 2)->default(0.00);
                $table->decimal('cash_difference', 12, 2)->default(0.00);
                $table->decimal('total_sales', 12, 2)->default(0.00);
                $table->unsignedInteger('total_transactions')->default(0);
                $table->string('status', 20)->default('buka');
                $table->boolean('is_auto_closed')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('pengguna')->onDelete('set null');
            });
        }

        // 15. KAS (lebur cash_movements + cash_adjustments)
        if (! Schema::hasTable('kas')) {
            Schema::create('kas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('shift_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('type', 20); // masuk, keluar, penyesuaian
                $table->decimal('amount', 12, 2);
                $table->string('reason');
                $table->timestamps();

                $table->foreign('shift_id')->references('id')->on('shift')->onDelete('set null');
                $table->foreign('user_id')->references('id')->on('pengguna')->onDelete('set null');
            });
        }

        // 16. STOK_RIWAYAT (lebur stock_logs + stock_movements)
        if (! Schema::hasTable('stok_riwayat')) {
            Schema::create('stok_riwayat', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('menu_id')->nullable();
                $table->unsignedBigInteger('stock_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('actor')->nullable();
                $table->string('item_name')->nullable();
                $table->string('type', 50);
                $table->decimal('quantity', 12, 2)->default(0.00);
                $table->decimal('stock_before', 12, 2)->nullable();
                $table->decimal('stock_after', 12, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('menu_id')->references('id')->on('menu')->onDelete('set null');
                $table->foreign('stock_id')->references('id')->on('stok')->onDelete('set null');
                $table->foreign('user_id')->references('id')->on('pengguna')->onDelete('set null');
            });
        }

        // 17. PESANAN (orders)
        if (! Schema::hasTable('pesanan')) {
            Schema::create('pesanan', function (Blueprint $table) {
                $table->id();
                $table->string('order_code')->unique();
                $table->string('customer_name');
                $table->integer('table_number')->nullable();
                $table->string('order_type')->default('dine_in');
                $table->string('source', 20)->default('kasir');
                $table->decimal('subtotal', 12, 2)->default(0.00);
                $table->decimal('tax', 12, 2)->default(0.00);
                $table->decimal('service_charge', 12, 2)->default(0.00);
                $table->decimal('discount', 12, 2)->default(0.00);
                $table->unsignedBigInteger('promo_id')->nullable();
                $table->unsignedBigInteger('cash_shift_id')->nullable();
                $table->decimal('total', 12, 2)->default(0.00);
                $table->string('payment_method')->nullable();
                $table->string('payment_status')->default('menunggu_pembayaran');
                $table->boolean('stock_deducted')->default(false);
                $table->boolean('stock_returned')->default(false);
                $table->string('order_status')->default('baru');
                $table->string('cashier_name')->nullable();
                $table->unsignedBigInteger('cashier_id')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->datetime('received_at')->nullable();
                $table->text('cancel_reason')->nullable();
                $table->timestamps();

                $table->foreign('promo_id')->references('id')->on('promo')->onDelete('set null');
                $table->foreign('cash_shift_id')->references('id')->on('shift')->onDelete('set null');
                $table->foreign('cashier_id')->references('id')->on('pengguna')->onDelete('set null');
            });
        }

        // 18. PESANAN_ITEM (order_items)
        if (! Schema::hasTable('pesanan_item')) {
            Schema::create('pesanan_item', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('menu_id')->nullable();
                $table->unsignedBigInteger('variant_id')->nullable();
                $table->string('name');
                $table->decimal('price', 12, 2);
                $table->integer('quantity');
                $table->decimal('subtotal', 12, 2);
                $table->string('notes')->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('pesanan')->onDelete('cascade');
                $table->foreign('menu_id')->references('id')->on('menu')->onDelete('set null');
                $table->foreign('variant_id')->references('id')->on('menu_varian')->onDelete('set null');
            });
        }

        // 19. PESANAN_RIWAYAT (order_timelines)
        if (! Schema::hasTable('pesanan_riwayat')) {
            Schema::create('pesanan_riwayat', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->string('status');
                $table->string('actor')->nullable();
                $table->text('description');
                $table->timestamp('logged_at')->useCurrent();

                $table->foreign('order_id')->references('id')->on('pesanan')->onDelete('cascade');
            });
        }

        // 20. PEMBAYARAN (payments)
        if (! Schema::hasTable('pembayaran')) {
            Schema::create('pembayaran', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('cashier_id')->nullable();
                $table->string('method', 30);
                $table->decimal('amount', 12, 2);
                $table->decimal('cash_received', 12, 2)->default(0.00);
                $table->decimal('change_given', 12, 2)->default(0.00);
                $table->string('reference')->nullable();
                $table->string('status', 30)->default('berhasil');
                $table->datetime('paid_at')->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('pesanan')->onDelete('cascade');
                $table->foreign('cashier_id')->references('id')->on('pengguna')->onDelete('set null');
            });
        }

        // 21. RESERVASI (reservations)
        if (! Schema::hasTable('reservasi')) {
            Schema::create('reservasi', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('customer_name');
                $table->string('phone')->nullable();
                $table->unsignedInteger('party_size')->default(1);
                $table->date('reservation_date');
                $table->time('reservation_time');
                $table->unsignedBigInteger('table_id')->nullable();
                $table->string('status', 20)->default('menunggu');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('table_id')->references('id')->on('meja')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('pengguna')->onDelete('set null');
            });
        }

        // 22. LOG_AKTIVITAS (activity_logs)
        if (! Schema::hasTable('log_aktivitas')) {
            Schema::create('log_aktivitas', function (Blueprint $table) {
                $table->id();
                $table->string('user_name');
                $table->string('role')->default('admin');
                $table->text('activity');
                $table->string('activity_type')->default('sistem');
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }

        // 23. PERAN (roles)
        if (! Schema::hasTable('peran')) {
            Schema::create('peran', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });
        }

        // 24. IZIN (permissions)
        if (! Schema::hasTable('izin')) {
            Schema::create('izin', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            });
        }

        // 25. PERAN_IZIN (role_has_permissions)
        if (! Schema::hasTable('peran_izin')) {
            Schema::create('peran_izin', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');
                $table->primary(['permission_id', 'role_id']);
                $table->foreign('permission_id')->references('id')->on('izin')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('peran')->onDelete('cascade');
            });
        }

        // 26. PENGGUNA_PERAN (model_has_roles)
        if (! Schema::hasTable('pengguna_peran')) {
            Schema::create('pengguna_peran', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type']);
                $table->primary(['role_id', 'model_id', 'model_type']);
                $table->foreign('role_id')->references('id')->on('peran')->onDelete('cascade');
            });
        }

        // 27. PENGGUNA_IZIN (model_has_permissions)
        if (! Schema::hasTable('pengguna_izin')) {
            Schema::create('pengguna_izin', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type']);
                $table->primary(['permission_id', 'model_id', 'model_type']);
                $table->foreign('permission_id')->references('id')->on('izin')->onDelete('cascade');
            });
        }

        // DATA COPY PHASE (dari tabel lama ke tabel baru)
        if (Schema::hasTable('users') && DB::table('pengguna')->count() === 0) {
            DB::statement('INSERT INTO `pengguna` (`id`, `name`, `username`, `email`, `role`, `phone`, `photo_path`, `status`, `avatar`, `last_login_at`, `email_verified_at`, `password`, `remember_token`, `is_active`, `created_at`, `updated_at`) SELECT `id`, `name`, `username`, `email`, `role`, `phone`, `photo_path`, `status`, `avatar`, `last_login_at`, `email_verified_at`, `password`, `remember_token`, `is_active`, `created_at`, `updated_at` FROM `users`');
        }

        if (Schema::hasTable('categories') && DB::table('kategori')->count() === 0) {
            DB::statement('INSERT INTO `kategori` (`id`, `name`, `icon`, `order`, `is_active`, `description`, `created_at`, `updated_at`) SELECT `id`, `name`, `icon`, `order`, `is_active`, `description`, `created_at`, `updated_at` FROM `categories`');
        }

        if (Schema::hasTable('menus') && DB::table('menu')->count() === 0) {
            DB::statement('INSERT INTO `menu` (`id`, `merged_into_menu_id`, `variant_id`, `category_id`, `name`, `price`, `description`, `image`, `is_available`, `is_best_seller`, `stock`, `min_stock`, `unit`, `sold_count`, `created_at`, `updated_at`, `deleted_at`) SELECT `id`, `merged_into_menu_id`, `variant_id`, `category_id`, `name`, `price`, `description`, `image`, `is_available`, `is_best_seller`, `stock`, `min_stock`, `unit`, `sold_count`, `created_at`, `updated_at`, `deleted_at` FROM `menus`');
        }

        if (Schema::hasTable('menu_variants') && DB::table('menu_varian')->count() === 0) {
            DB::statement('INSERT INTO `menu_varian` (`id`, `menu_id`, `name`, `size`, `price`, `is_available`, `is_default`, `sort_order`, `created_at`, `updated_at`) SELECT `id`, `menu_id`, `name`, `size`, `price`, `is_available`, `is_default`, `sort_order`, `created_at`, `updated_at` FROM `menu_variants`');
        }

        if (Schema::hasTable('favorites') && DB::table('favorit')->count() === 0) {
            DB::statement('INSERT INTO `favorit` (`id`, `user_id`, `menu_id`, `created_at`, `updated_at`) SELECT `id`, `user_id`, `menu_id`, `created_at`, `updated_at` FROM `favorites`');
        }

        if (Schema::hasTable('tables') && DB::table('meja')->count() === 0) {
            DB::statement('INSERT INTO `meja` (`id`, `number`, `capacity`, `status`, `location_notes`, `created_at`, `updated_at`) SELECT `id`, `number`, `capacity`, `status`, `location_notes`, `created_at`, `updated_at` FROM `tables`');
        }

        if (Schema::hasTable('payment_methods') && DB::table('metode_bayar')->count() === 0) {
            DB::statement('INSERT INTO `metode_bayar` (`id`, `name`, `code`, `type`, `description`, `instructions`, `is_active`, `sort_order`, `created_at`, `updated_at`) SELECT `id`, `name`, `code`, `type`, `description`, `instructions`, `is_active`, `sort_order`, `created_at`, `updated_at` FROM `payment_methods`');
        }

        if (Schema::hasTable('promos') && DB::table('promo')->count() === 0) {
            DB::statement('INSERT INTO `promo` (`id`, `title`, `code`, `description`, `type`, `value`, `applies_to`, `target_category_id`, `target_menu_id`, `start_date`, `end_date`, `banner`, `is_active`, `min_spend`, `quota`, `created_at`, `updated_at`) SELECT `id`, `title`, `code`, `description`, `type`, `value`, `applies_to`, `target_category_id`, `target_menu_id`, `start_date`, `end_date`, `banner`, `is_active`, `min_spend`, `quota`, `created_at`, `updated_at` FROM `promos`');
        }

        if (Schema::hasTable('cafe_settings') && DB::table('pengaturan')->count() === 0) {
            DB::statement('INSERT INTO `pengaturan` (`id`, `key`, `value`, `created_at`, `updated_at`) SELECT `id`, `key`, `value`, `created_at`, `updated_at` FROM `cafe_settings`');
        }

        if (Schema::hasTable('stocks') && DB::table('stok')->count() === 0) {
            DB::statement('INSERT INTO `stok` (`id`, `name`, `unit`, `quantity`, `min_quantity`, `is_active`, `created_at`, `updated_at`) SELECT `id`, `name`, `unit`, `quantity`, `min_quantity`, `is_active`, `created_at`, `updated_at` FROM `stocks`');
        }

        if (Schema::hasTable('suppliers') && DB::table('pemasok')->count() === 0) {
            DB::statement('INSERT INTO `pemasok` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `notes`, `is_active`, `created_at`, `updated_at`) SELECT `id`, `name`, `contact_person`, `phone`, `email`, `address`, `notes`, `is_active`, `created_at`, `updated_at` FROM `suppliers`');
        }

        if (Schema::hasTable('stock_supplier') && DB::table('stok_pemasok')->count() === 0) {
            DB::statement('INSERT INTO `stok_pemasok` (`stock_id`, `supplier_id`) SELECT `stock_id`, `supplier_id` FROM `stock_supplier`');
        }

        if (Schema::hasTable('recipes') && DB::table('resep')->count() === 0) {
            DB::statement('INSERT INTO `resep` (`id`, `menu_id`, `menu_variant_id`, `stock_id`, `amount`, `created_at`, `updated_at`) SELECT `id`, `menu_id`, `menu_variant_id`, `stock_id`, `amount`, `created_at`, `updated_at` FROM `recipes`');
        }

        if (Schema::hasTable('cash_shifts') && DB::table('shift')->count() === 0) {
            DB::statement('INSERT INTO `shift` (`id`, `user_id`, `opened_at`, `closed_at`, `opening_cash`, `opening_cash_source`, `expected_cash`, `closing_cash`, `cash_difference`, `total_sales`, `total_transactions`, `status`, `is_auto_closed`, `notes`, `created_at`, `updated_at`) SELECT `id`, `user_id`, `opened_at`, `closed_at`, `opening_cash`, `opening_cash_source`, `expected_cash`, `closing_cash`, `cash_difference`, `total_sales`, `total_transactions`, `status`, `is_auto_closed`, `notes`, `created_at`, `updated_at` FROM `cash_shifts`');
        }

        // Peleburan KAS (dari cash_movements + cash_adjustments)
        if (Schema::hasTable('cash_movements') && DB::table('kas')->count() === 0) {
            DB::statement('INSERT INTO `kas` (`id`, `shift_id`, `user_id`, `type`, `amount`, `reason`, `created_at`, `updated_at`) SELECT `id`, `cash_shift_id`, `user_id`, `type`, `amount`, `reason`, `created_at`, `updated_at` FROM `cash_movements`');
        }
        if (Schema::hasTable('cash_adjustments')) {
            $adjustments = DB::table('cash_adjustments')->get();
            foreach ($adjustments as $adj) {
                DB::table('kas')->insert([
                    'shift_id' => $adj->applied_to_shift_id,
                    'user_id' => $adj->user_id,
                    'type' => 'penyesuaian',
                    'amount' => $adj->amount,
                    'reason' => $adj->reason,
                    'created_at' => $adj->created_at,
                    'updated_at' => $adj->updated_at,
                ]);
            }
        }

        // Peleburan STOK_RIWAYAT (dari stock_logs + stock_movements)
        if (Schema::hasTable('stock_logs') && DB::table('stok_riwayat')->count() === 0) {
            DB::statement('INSERT INTO `stok_riwayat` (`id`, `menu_id`, `item_name`, `type`, `quantity`, `stock_before`, `stock_after`, `notes`, `actor`, `created_at`, `updated_at`) SELECT `id`, `menu_id`, `item_name`, `type`, `quantity`, `stock_before`, `stock_after`, `notes`, `actor`, `created_at`, `updated_at` FROM `stock_logs`');
        }
        if (Schema::hasTable('stock_movements')) {
            $movements = DB::table('stock_movements')->get();
            foreach ($movements as $m) {
                DB::table('stok_riwayat')->insert([
                    'stock_id' => $m->stock_id,
                    'user_id' => $m->user_id,
                    'type' => $m->type,
                    'quantity' => $m->quantity,
                    'stock_after' => $m->stock_after,
                    'notes' => $m->note,
                    'created_at' => $m->created_at,
                    'updated_at' => $m->updated_at,
                ]);
            }
        }

        if (Schema::hasTable('orders') && DB::table('pesanan')->count() === 0) {
            DB::statement('INSERT INTO `pesanan` (`id`, `order_code`, `customer_name`, `table_number`, `order_type`, `source`, `subtotal`, `tax`, `service_charge`, `discount`, `promo_id`, `cash_shift_id`, `total`, `payment_method`, `payment_status`, `stock_deducted`, `stock_returned`, `order_status`, `cashier_name`, `cashier_id`, `paid_at`, `received_at`, `cancel_reason`, `created_at`, `updated_at`) SELECT `id`, `order_code`, `customer_name`, `table_number`, `order_type`, `source`, `subtotal`, `tax`, `service_charge`, `discount`, `promo_id`, `cash_shift_id`, `total`, `payment_method`, `payment_status`, `stock_deducted`, `stock_returned`, `order_status`, `cashier_name`, `cashier_id`, `paid_at`, `received_at`, `cancel_reason`, `created_at`, `updated_at` FROM `orders`');
        }

        if (Schema::hasTable('order_items') && DB::table('pesanan_item')->count() === 0) {
            DB::statement('INSERT INTO `pesanan_item` (`id`, `order_id`, `menu_id`, `variant_id`, `name`, `price`, `quantity`, `subtotal`, `notes`, `created_at`, `updated_at`) SELECT `id`, `order_id`, `menu_id`, `variant_id`, `name`, `price`, `quantity`, `subtotal`, `notes`, `created_at`, `updated_at` FROM `order_items`');
        }

        if (Schema::hasTable('order_timelines') && DB::table('pesanan_riwayat')->count() === 0) {
            DB::statement('INSERT INTO `pesanan_riwayat` (`id`, `order_id`, `status`, `actor`, `description`, `logged_at`) SELECT `id`, `order_id`, `status`, `actor`, `description`, `logged_at` FROM `order_timelines`');
        }

        if (Schema::hasTable('payments') && DB::table('pembayaran')->count() === 0) {
            DB::statement('INSERT INTO `pembayaran` (`id`, `order_id`, `cashier_id`, `method`, `amount`, `cash_received`, `change_given`, `reference`, `status`, `paid_at`, `created_at`, `updated_at`) SELECT `id`, `order_id`, `cashier_id`, `method`, `amount`, `cash_received`, `change_given`, `reference`, `status`, `paid_at`, `created_at`, `updated_at` FROM `payments`');
        }

        if (Schema::hasTable('reservations') && DB::table('reservasi')->count() === 0) {
            DB::statement('INSERT INTO `reservasi` (`id`, `code`, `customer_name`, `phone`, `party_size`, `reservation_date`, `reservation_time`, `table_id`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) SELECT `id`, `code`, `customer_name`, `phone`, `party_size`, `reservation_date`, `reservation_time`, `table_id`, `status`, `notes`, `created_by`, `created_at`, `updated_at` FROM `reservations`');
        }

        if (Schema::hasTable('activity_logs') && DB::table('log_aktivitas')->count() === 0) {
            DB::statement('INSERT INTO `log_aktivitas` (`id`, `user_name`, `role`, `activity`, `activity_type`, `ip_address`, `created_at`, `updated_at`) SELECT `id`, `user_name`, `role`, `activity`, `activity_type`, `ip_address`, `created_at`, `updated_at` FROM `activity_logs`');
        }

        if (Schema::hasTable('roles') && DB::table('peran')->count() === 0) {
            DB::statement('INSERT INTO `peran` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) SELECT `id`, `name`, `guard_name`, `created_at`, `updated_at` FROM `roles`');
        }

        if (Schema::hasTable('permissions') && DB::table('izin')->count() === 0) {
            DB::statement('INSERT INTO `izin` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) SELECT `id`, `name`, `guard_name`, `created_at`, `updated_at` FROM `permissions`');
        }

        if (Schema::hasTable('role_has_permissions') && DB::table('peran_izin')->count() === 0) {
            DB::statement('INSERT INTO `peran_izin` (`permission_id`, `role_id`) SELECT `permission_id`, `role_id` FROM `role_has_permissions`');
        }

        if (Schema::hasTable('model_has_roles') && DB::table('pengguna_peran')->count() === 0) {
            DB::statement('INSERT INTO `pengguna_peran` (`role_id`, `model_type`, `model_id`) SELECT `role_id`, `model_type`, `model_id` FROM `model_has_roles`');
        }

        if (Schema::hasTable('model_has_permissions') && DB::table('pengguna_izin')->count() === 0) {
            DB::statement('INSERT INTO `pengguna_izin` (`permission_id`, `model_type`, `model_id`) SELECT `permission_id`, `model_type`, `model_id` FROM `model_has_permissions`');
        }

        // VERIFICATION PHASE: pastikan jumlah baris dan total nominal sebelum = sesudah
        $verifications = [
            'users' => 'pengguna',
            'categories' => 'kategori',
            'menus' => 'menu',
            'menu_variants' => 'menu_varian',
            'favorites' => 'favorit',
            'tables' => 'meja',
            'payment_methods' => 'metode_bayar',
            'promos' => 'promo',
            'cafe_settings' => 'pengaturan',
            'stocks' => 'stok',
            'suppliers' => 'pemasok',
            'stock_supplier' => 'stok_pemasok',
            'recipes' => 'resep',
            'cash_shifts' => 'shift',
            'orders' => 'pesanan',
            'order_items' => 'pesanan_item',
            'order_timelines' => 'pesanan_riwayat',
            'payments' => 'pembayaran',
            'reservations' => 'reservasi',
            'activity_logs' => 'log_aktivitas',
        ];

        foreach ($verifications as $old => $new) {
            if (Schema::hasTable($old)) {
                $oldCount = DB::table($old)->count();
                $newCount = DB::table($new)->count();
                if ($oldCount !== $newCount) {
                    throw new RuntimeException("Verifikasi gagal: Jumlah baris {$old} ({$oldCount}) != {$new} ({$newCount})");
                }
            }
        }

        if (Schema::hasTable('orders')) {
            $sumOldOrders = (float) DB::table('orders')->sum('total');
            $sumNewOrders = (float) DB::table('pesanan')->sum('total');
            if (abs($sumOldOrders - $sumNewOrders) > 0.01) {
                throw new RuntimeException("Verifikasi gagal: Total pesanan {$sumOldOrders} != {$sumNewOrders}");
            }
        }

        if (Schema::hasTable('payments')) {
            $sumOldPayments = (float) DB::table('payments')->sum('amount');
            $sumNewPayments = (float) DB::table('pembayaran')->sum('amount');
            if (abs($sumOldPayments - $sumNewPayments) > 0.01) {
                throw new RuntimeException("Verifikasi gagal: Total pembayaran {$sumOldPayments} != {$sumNewPayments}");
            }
        }

        // RENAME OLD TABLES TO ARSIP PHASE
        $tablesToArchive = [
            'users', 'categories', 'menus', 'menu_variants', 'favorites',
            'tables', 'cafe_tables', 'recipes', 'payment_methods', 'promos',
            'settings', 'cafe_settings', 'stocks', 'suppliers', 'stock_supplier',
            'stock_logs', 'stock_movements', 'orders', 'order_items', 'order_timelines',
            'payments', 'cash_shifts', 'cash_movements', 'cash_adjustments',
            'reservations', 'activity_logs', 'roles', 'permissions',
            'role_has_permissions', 'model_has_roles', 'model_has_permissions',
        ];

        foreach ($tablesToArchive as $tbl) {
            if (Schema::hasTable($tbl) && ! Schema::hasTable('arsip_'.$tbl)) {
                Schema::rename($tbl, 'arsip_'.$tbl);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $newTables = [
            'pengguna_izin', 'pengguna_peran', 'peran_izin', 'izin', 'peran',
            'log_aktivitas', 'reservasi', 'pembayaran', 'pesanan_riwayat', 'pesanan_item', 'pesanan',
            'stok_riwayat', 'kas', 'shift', 'resep', 'stok_pemasok', 'pemasok', 'stok',
            'pengaturan', 'promo', 'metode_bayar', 'meja', 'favorit', 'menu_varian', 'menu', 'kategori', 'pengguna',
        ];

        foreach ($newTables as $tbl) {
            Schema::dropIfExists($tbl);
        }

        $tablesToRestore = [
            'users', 'categories', 'menus', 'menu_variants', 'favorites',
            'tables', 'cafe_tables', 'recipes', 'payment_methods', 'promos',
            'settings', 'cafe_settings', 'stocks', 'suppliers', 'stock_supplier',
            'stock_logs', 'stock_movements', 'orders', 'order_items', 'order_timelines',
            'payments', 'cash_shifts', 'cash_movements', 'cash_adjustments',
            'reservations', 'activity_logs', 'roles', 'permissions',
            'role_has_permissions', 'model_has_roles', 'model_has_permissions',
        ];

        foreach ($tablesToRestore as $tbl) {
            if (Schema::hasTable('arsip_'.$tbl) && ! Schema::hasTable($tbl)) {
                Schema::rename('arsip_'.$tbl, $tbl);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
