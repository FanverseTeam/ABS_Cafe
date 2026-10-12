<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = ['stock_id', 'user_id', 'type', 'quantity', 'stock_after', 'note'];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'stock_after' => 'decimal:2',
        ];
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array{label: string, color: string}
     */
    public function typeBadge(): array
    {
        return match ($this->type) {
            'masuk' => ['label' => 'Masuk', 'color' => 'success'],
            'keluar' => ['label' => 'Keluar', 'color' => 'danger'],
            default => ['label' => 'Penyesuaian', 'color' => 'info'],
        };
    }
}
