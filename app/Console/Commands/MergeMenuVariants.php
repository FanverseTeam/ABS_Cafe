<?php

namespace App\Console\Commands;

use App\Models\Menu;
use App\Services\MenuMergeService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('menu:merge-variants {--dry-run : Tampilkan rencana tanpa mengubah data} {--apply : Jalankan penggabungan sungguhan} {--rollback= : ID menu induk yang akan dibatalkan} {--rollback-all : Batalkan semua penggabungan}')]
#[Description('Deteksi dan gabungkan menu Hot/Ice menjadi satu menu dengan varian')]
class MergeMenuVariants extends Command
{
    public function handle(MenuMergeService $merge): int
    {
        if ($this->option('rollback') || $this->option('rollback-all')) {
            return $this->rollback($merge);
        }

        $analysis = $merge->analyze();

        $this->renderGroups($analysis['groups']);
        $this->renderAmbiguous($analysis['ambiguous']);
        $this->renderCategorySuggestions($analysis['category_suggestions']);

        $this->newLine();
        $this->line('Grup siap digabung: <info>'.count($analysis['groups']).'</info>');
        $this->line('Kasus dilewati/meragukan: <comment>'.count($analysis['ambiguous']).'</comment>');

        if (! $this->option('apply')) {
            $this->newLine();
            $this->comment('Mode dry-run. Tidak ada data yang diubah. Jalankan dengan --apply setelah menyetujui laporan ini.');

            return self::SUCCESS;
        }

        if ($analysis['groups'] === []) {
            $this->info('Tidak ada grup yang perlu digabung.');

            return self::SUCCESS;
        }

        $merged = $merge->mergeAllDetected();

        foreach ($merged as $menu) {
            $this->line("Digabung: <info>{$menu->name}</info> (#{$menu->id})");
        }

        $this->info('Selesai. '.count($merged).' grup menu berhasil digabung.');

        return self::SUCCESS;
    }

    /**
     * @param  array<int, array<string, mixed>>  $groups
     */
    private function renderGroups(array $groups): void
    {
        $this->info('Rencana penggabungan');
        $this->newLine();

        if ($groups === []) {
            $this->line('Tidak ada pasangan yang memenuhi aturan ketat.');

            return;
        }

        $this->table(
            ['Kategori', 'Nama Baru', 'Varian', 'Menu Lama (harga)'],
            array_map(fn (array $group) => [
                $group['category_name'],
                $group['display_name'],
                implode(', ', $group['variant_labels']),
                collect($group['menus'])
                    ->map(fn (array $menu) => $menu['name'].' (Rp '.number_format($menu['price'], 0, ',', '.').')')
                    ->implode(PHP_EOL),
            ], $groups)
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $ambiguous
     */
    private function renderAmbiguous(array $ambiguous): void
    {
        if ($ambiguous === []) {
            return;
        }

        $this->newLine();
        $this->warn('Kasus meragukan (tidak digabung)');

        $this->table(
            ['Kategori', 'Nama Dasar', 'Alasan', 'Menu'],
            array_map(fn (array $row) => [
                $row['category_name'],
                $row['base'],
                $row['reason'],
                collect($row['menus'])->pluck('name')->implode(', '),
            ], $ambiguous)
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $suggestions
     */
    private function renderCategorySuggestions(array $suggestions): void
    {
        if ($suggestions === []) {
            return;
        }

        $this->newLine();
        $this->comment('Saran penyatuan kategori (perlu konfirmasi manual, tidak diubah otomatis)');

        $this->table(
            ['Kategori Saat Ini', 'Nama Kategori yang Disarankan'],
            array_map(fn (array $row) => [
                implode(', ', $row['categories']),
                $row['suggested_name'],
            ], $suggestions)
        );
    }

    private function rollback(MenuMergeService $merge): int
    {
        $parents = $this->option('rollback-all')
            ? Menu::withTrashed()
                ->whereIn('id', Menu::withTrashed()->whereNotNull('merged_into_menu_id')->pluck('merged_into_menu_id'))
                ->get()
            : Menu::withTrashed()->whereKey((int) $this->option('rollback'))->get();

        if ($parents->isEmpty()) {
            $this->warn('Menu induk tidak ditemukan atau tidak memiliki riwayat penggabungan.');

            return self::SUCCESS;
        }

        $restored = 0;

        foreach ($parents as $parent) {
            $count = $merge->rollback($parent);
            $restored += $count;
            $this->line("Rollback <info>{$parent->name}</info>: {$count} menu lama dipulihkan.");
        }

        $this->info("Rollback selesai. {$restored} menu lama dipulihkan.");

        return self::SUCCESS;
    }
}
