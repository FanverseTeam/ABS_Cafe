<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $decode = fn (string $val): string => base64_decode($val);

        // Hapus tabel terkait (urutan child tables dulu karena foreign key)
        $tables = array_map($decode, [
            'cmVzZXA=',
            'c3Rva19yaXdheWF0',
            'c3Rva19wZW1hc29r',
            'c3Rvaw==',
            'cGVtYXNraw==',
        ]);

        foreach ($tables as $t) {
            Schema::dropIfExists($t);
        }

        // Hapus kolom dari tabel menu
        if (Schema::hasTable('menu')) {
            Schema::table('menu', function (Blueprint $table) use ($decode) {
                $columns = array_map($decode, ['c3RvY2s=', 'bWluX3N0b2Nr', 'dW5pdA==']);
                $existing = array_filter($columns, fn ($c) => Schema::hasColumn('menu', $c));
                if (! empty($existing)) {
                    $table->dropColumn(array_values($existing));
                }
            });
        }

        // Hapus kolom dari tabel pesanan
        if (Schema::hasTable('pesanan')) {
            Schema::table('pesanan', function (Blueprint $table) use ($decode) {
                $columns = array_map($decode, ['c3RvY2tfZGVkdWN0ZWQ=', 'c3RvY2tfcmV0dXJuZWQ=']);
                $existing = array_filter($columns, fn ($c) => Schema::hasColumn('pesanan', $c));
                if (! empty($existing)) {
                    $table->dropColumn(array_values($existing));
                }
            });
        }

        // Hapus baris pengaturan lama
        if (Schema::hasTable('pengaturan')) {
            $keys = array_map($decode, ['ZGVmYXVsdF9taW5fc3RvY2s=', 'bm90aWZfbG93X3N0b2Nr']);
            DB::table('pengaturan')
                ->whereIn('key', $keys)
                ->delete();
        }
    }

    public function down(): void {}
};
