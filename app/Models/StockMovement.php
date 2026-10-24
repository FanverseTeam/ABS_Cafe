<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends StokRiwayat
{
    use HasFactory;

    protected $table = 'stok_riwayat';

    protected static function booted(): void
    {
        static::addGlobalScope('material_movement', function (Builder $builder) {
            $builder->whereNotNull('stock_id');
        });
    }

    public function getNoteAttribute()
    {
        return $this->attributes['notes'] ?? null;
    }

    public function setNoteAttribute($value): void
    {
        $this->attributes['notes'] = $value;
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
