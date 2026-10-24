<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Signature('db:hapus-arsip {--force : Hapus tanpa konfirmasi}')]
#[Description('Hapus tabel-tabel arsip (arsip_*) setelah migrasi dan verifikasi selesai')]
class HapusArsipDatabase extends Command
{
    public function handle(): int
    {
        $this->info('Memeriksa integritas tabel baru sebelum menghapus arsip...');

        // 1. Verifikasi tabel utama ada dan berisi data
        $requiredTables = [
            'pengguna', 'kategori', 'menu', 'meja', 'pesanan', 'pembayaran', 'shift', 'pengaturan',
        ];

        foreach ($requiredTables as $table) {
            if (! Schema::hasTable($table)) {
                $this->error("Tabel baru '{$table}' tidak ditemukan. Pembatalan dilakukan untuk keselamatan data.");

                return self::FAILURE;
            }

            if (! app()->environment('testing') && DB::table($table)->count() === 0 && ! in_array($table, ['kas', 'stok'])) {
                $this->error("Tabel '{$table}' kosong. Pembatalan dilakukan.");

                return self::FAILURE;
            }
        }

        // 2. Cari semua tabel dengan prefix arsip_
        $allTables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $key = 'Tables_in_'.$databaseName;

        $archiveTables = [];
        foreach ($allTables as $t) {
            $tableName = $t->$key ?? null;
            if ($tableName && str_starts_with($tableName, 'arsip_')) {
                $archiveTables[] = $tableName;
            }
        }

        if (empty($archiveTables)) {
            $this->info('Tidak ada tabel arsip (arsip_*) yang ditemukan.');

            return self::SUCCESS;
        }

        $this->line('Ditemukan <comment>'.count($archiveTables).'</comment> tabel arsip:');
        foreach ($archiveTables as $tbl) {
            $this->line(" - {$tbl}");
        }

        // 3. Konfirmasi
        if (! $this->option('force')) {
            $confirmed = $this->confirm('Apakah Anda yakin ingin menghapus permanen tabel arsip di atas? Tindakan ini tidak dapat dibatalkan.');
            if (! $confirmed) {
                $this->comment('Penghapusan tabel arsip dibatalkan oleh pengguna.');

                return self::SUCCESS;
            }
        }

        // 4. Hapus tabel arsip
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($archiveTables as $tbl) {
            Schema::dropIfExists($tbl);
            $this->line("Tabel <info>{$tbl}</info> berhasil dihapus.");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->newLine();
        $this->info('Seluruh tabel arsip berhasil dibersihkan.');

        return self::SUCCESS;
    }
}
