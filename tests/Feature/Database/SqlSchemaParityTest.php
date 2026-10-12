<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SqlSchemaParityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Parse `CREATE TABLE` + column definitions from the shipping SQL dump.
     *
     * @return array<string, array<int, string>>
     */
    private function dumpSchema(): array
    {
        $path = base_path('db_abscafe.sql');
        $this->assertFileExists($path, 'File db_abscafe.sql tidak ditemukan.');

        $tables = [];
        $current = null;

        foreach (file($path, FILE_IGNORE_NEW_LINES) as $line) {
            if (preg_match('/^CREATE TABLE `([^`]+)`/', $line, $m)) {
                $current = $m[1];
                $tables[$current] = [];

                continue;
            }

            if ($current === null) {
                continue;
            }

            if (str_starts_with($line, ')')) {
                $current = null;

                continue;
            }

            if (preg_match('/^  `([^`]+)` /', $line, $m)) {
                $tables[$current][] = $m[1];
            }
        }

        return $tables;
    }

    public function test_sql_dump_and_migrations_define_the_same_tables(): void
    {
        $dump = $this->dumpSchema();
        $this->assertNotEmpty($dump, 'Tidak ada CREATE TABLE yang terbaca dari db_abscafe.sql.');

        foreach (array_keys($dump) as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Tabel `{$table}` ada di db_abscafe.sql tetapi tidak dibuat oleh migrasi."
            );
        }
    }

    public function test_sql_dump_and_migrations_define_the_same_columns(): void
    {
        foreach ($this->dumpSchema() as $table => $sqlColumns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $dbColumns = Schema::getColumnListing($table);
            sort($sqlColumns);
            sort($dbColumns);

            $this->assertSame(
                $sqlColumns,
                $dbColumns,
                "Kolom tabel `{$table}` berbeda antara db_abscafe.sql dan migrasi."
            );
        }
    }
}
