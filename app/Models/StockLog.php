<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends StokRiwayat
{
    use HasFactory;

    protected $table = 'stok_riwayat';

    protected static function booted(): void
    {
        static::addGlobalScope('menu_log', function (Builder $builder) {
            $builder->whereNotNull('menu_id');
        });
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
