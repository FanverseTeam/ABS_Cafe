<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSimplificationMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_tables_exist_and_models_operate_on_them(): void
    {
        $newTables = [
            'pengguna', 'kategori', 'menu', 'menu_varian', 'favorit', 'meja',
            'metode_bayar', 'promo', 'pengaturan', 'stok', 'pemasok',
            'stok_pemasok', 'resep', 'shift', 'kas', 'stok_riwayat',
            'pesanan', 'pesanan_item', 'pesanan_riwayat', 'pembayaran',
            'reservasi', 'log_aktivitas', 'peran', 'izin', 'peran_izin',
            'pengguna_peran', 'pengguna_izin',
        ];

        foreach ($newTables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabel {$table} harus ada di database.");
        }

        $user = User::factory()->create();
        $this->assertDatabaseHas('pengguna', ['id' => $user->id]);

        $category = Category::factory()->create();
        $this->assertDatabaseHas('kategori', ['id' => $category->id]);

        $menu = Menu::factory()->for($category)->create();
        $this->assertDatabaseHas('menu', ['id' => $menu->id]);

        $tableModel = Table::factory()->create();
        $this->assertDatabaseHas('meja', ['id' => $tableModel->id]);

        $order = Order::factory()->create();
        $this->assertDatabaseHas('pesanan', ['id' => $order->id]);
    }

    public function test_migration_is_idempotent(): void
    {
        $migration = require database_path('migrations/2026_10_24_210000_sederhanakan_dan_lebur_tabel_database.php');

        // Menjalankan up kedua kali tidak boleh melempar exception
        $migration->up();
        $this->assertTrue(Schema::hasTable('menu'));
    }

    public function test_migration_down_restores_archived_tables(): void
    {
        $migration = require database_path('migrations/2026_10_24_210000_sederhanakan_dan_lebur_tabel_database.php');

        // Pastikan tabel baru ada
        $this->assertTrue(Schema::hasTable('menu'));

        // Jalankan down
        $migration->down();

        // Tabel baru harus hilang
        $this->assertFalse(Schema::hasTable('menu'));
        $this->assertFalse(Schema::hasTable('pengguna'));

        // Tabel arsip dikembalikan ke nama aslinya
        $this->assertTrue(Schema::hasTable('menus'));
        $this->assertTrue(Schema::hasTable('users'));

        // Jalankan up lagi untuk mengembalikan state
        $migration->up();
        $this->assertTrue(Schema::hasTable('menu'));
    }

    public function test_artisan_db_hapus_arsip_deletes_archive_tables(): void
    {
        // Buat dummy tabel arsip untuk memastikan command mendeteksi dan menghapusnya
        DB::statement('CREATE TABLE IF NOT EXISTS arsip_dummy_test (id INT AUTO_INCREMENT PRIMARY KEY)');
        $this->assertTrue(Schema::hasTable('arsip_dummy_test'));

        $this->artisan('db:hapus-arsip', ['--force' => true])
            ->assertExitCode(0);

        $this->assertFalse(Schema::hasTable('arsip_dummy_test'));
        $this->assertTrue(Schema::hasTable('menu'));
        $this->assertTrue(Schema::hasTable('pengguna'));
    }
}
