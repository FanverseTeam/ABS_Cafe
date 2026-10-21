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
        // 1. Hapus tabel redundan / duplikat yang tidak terpakai
        Schema::dropIfExists('cafe_tables');
        Schema::dropIfExists('settings');

        // 2. Berikan komentar deskripsi pada setiap tabel agar mudah dipahami
        if (DB::getDriverName() === 'mysql') {
            $tableComments = [
                'users' => 'Data akun pengguna, staf, kasir, admin, dan owner cafe',
                'categories' => 'Kategori menu makanan, minuman, dan produk cafe',
                'menus' => 'Katalog produk dan menu makanan/minuman yang dijual',
                'menu_variants' => 'Varian menu (ukuran, level rasa, dan harga khusus)',
                'tables' => 'Data meja cafe untuk pelanggan dine-in',
                'orders' => 'Transaksi pesanan pelanggan (dine-in dan take-away)',
                'order_items' => 'Rincian menu dan kuantitas dalam setiap pesanan',
                'order_timelines' => 'Catatan riwayat status perjalanan pesanan',
                'payments' => 'Pencatatan pembayaran kasir (tunai, QRIS, debit)',
                'payment_methods' => 'Master metode pembayaran yang didukung sistem',
                'cash_shifts' => 'Rekapitulasi shift kasir (buka/tutup kasir, saldo, penjualan)',
                'cash_movements' => 'Kas masuk/keluar operasional harian saat shift kasir',
                'cash_adjustments' => 'Penyesuaian saldo kas kasir oleh owner',
                'promos' => 'Data promo diskon, voucher, dan kampanye cafe',
                'reservations' => 'Data reservasi meja cafe oleh pelanggan',
                'stock_logs' => 'Riwayat pergerakan stok langsung per item menu',
                'stocks' => 'Inventaris master bahan baku dapur dan bar',
                'suppliers' => 'Master data vendor dan supplier bahan baku',
                'stock_supplier' => 'Relasi pivot antara bahan baku dan supplier',
                'stock_movements' => 'Log mutasi keluar, masuk, dan penyesuaian stok bahan baku',
                'recipes' => 'Resep takaran kebutuhan stok bahan baku per menu/varian',
                'favorites' => 'Daftar menu favorit pelanggan',
                'cafe_settings' => 'Konfigurasi umum dan pengaturan operasional ABS Cafe',
                'activity_logs' => 'Log audit riwayat aktivitas admin, kasir, dan sistem',
                'roles' => 'Master role hak akses pengguna',
                'permissions' => 'Master izin hak akses sistem',
                'model_has_roles' => 'Relasi penetapan role ke pengguna',
                'model_has_permissions' => 'Relasi penetapan izin langsung ke pengguna',
                'role_has_permissions' => 'Relasi penetapan izin ke masing-masing role',
            ];

            foreach ($tableComments as $table => $comment) {
                if (Schema::hasTable($table)) {
                    $escapedComment = addslashes($comment);
                    DB::statement("ALTER TABLE `{$table}` COMMENT = '{$escapedComment}'");
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('cafe_tables')) {
            Schema::create('cafe_tables', function (Blueprint $table) {
                $table->id();
                $table->string('number', 20)->unique();
                $table->unsignedInteger('capacity')->default(2);
                $table->enum('area', ['indoor', 'outdoor'])->default('indoor');
                $table->enum('status', ['kosong', 'terisi', 'dipesan'])->default('kosong');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('group', 50)->default('umum');
                $table->timestamps();
            });
        }
    }
};
