<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokRiwayat extends Model
{
    use HasFactory;

    protected $table = 'stok_riwayat';

    protected $fillable = [
        'menu_id',
        'stock_id',
        'user_id',
        'actor',
        'item_name',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeMenuLogs(Builder $query): Builder
    {
        return $query->whereNotNull('menu_id');
    }

    public function scopeMaterialMovements(Builder $query): Builder
    {
        return $query->whereNotNull('stock_id');
    }

    public function typeBadge(): array
    {
        return match ($this->type) {
            'masuk' => ['label' => 'Masuk', 'color' => 'success'],
            'keluar' => ['label' => 'Keluar', 'color' => 'danger'],
            default => ['label' => 'Koreksi', 'color' => 'info'],
        };
    }
}
