<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Text columns that may contain display text entered by users.
     *
     * @var array<string, list<string>>
     */
    private array $columns = [
        'activity_logs' => ['user_name', 'role', 'activity', 'activity_type'],
        'cafe_settings' => ['value'],
        'cafe_tables' => ['notes'],
        'cash_adjustments' => ['reason'],
        'cash_movements' => ['reason'],
        'cash_shifts' => ['opening_cash_source', 'notes'],
        'categories' => ['name', 'description', 'icon'],
        'menus' => ['name', 'description', 'unit'],
        'menu_variants' => ['name'],
        'orders' => ['customer_name', 'cancel_reason'],
        'order_items' => ['name', 'notes'],
        'order_timelines' => ['actor', 'description'],
        'payment_methods' => ['name', 'description', 'instructions'],
        'promos' => ['title', 'code', 'description'],
        'reservations' => ['customer_name', 'notes'],
        'settings' => ['value'],
        'stocks' => ['name', 'unit'],
        'stock_logs' => ['item_name', 'notes', 'actor'],
        'stock_movements' => ['note'],
        'suppliers' => ['name', 'contact_person', 'address', 'notes'],
        'tables' => ['location_notes'],
        'users' => ['name', 'phone'],
    ];

    private const EMOJI_PATTERN = '/[\x{1F000}-\x{1FAFF}\x{1F1E6}-\x{1F1FF}\x{1F3FB}-\x{1F3FF}\x{2600}-\x{27BF}\x{2300}-\x{23FF}\x{25A0}-\x{25FF}\x{2B00}-\x{2BFF}\x{FE0F}\x{200D}\x{20E3}\x{3030}\x{303D}\x{3297}\x{3299}]/u';

    public function up(): void
    {
        foreach ($this->columns as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::table($table)
                    ->select('id', $column)
                    ->orderBy('id')
                    ->chunkById(200, function ($rows) use ($table, $column): void {
                        foreach ($rows as $row) {
                            $value = $row->{$column};

                            if (! is_string($value)) {
                                continue;
                            }

                            $cleaned = $this->stripEmoji($value);

                            if ($cleaned !== $value) {
                                DB::table($table)
                                    ->where('id', $row->id)
                                    ->update([$column => $cleaned]);
                            }
                        }
                    });
            }
        }
    }

    public function down(): void
    {
        // Emoji yang sudah dibersihkan tidak dipulihkan.
    }

    private function stripEmoji(string $value): string
    {
        $cleaned = preg_replace(self::EMOJI_PATTERN, '', $value) ?? $value;
        $cleaned = preg_replace('/[ \t]{2,}/u', ' ', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/\s+([,.!?;:])/u', '$1', $cleaned) ?? $cleaned;

        return trim($cleaned);
    }
};
