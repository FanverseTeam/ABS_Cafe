<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuVariant extends Model
{
    use HasFactory;

    protected $fillable = ['menu_id', 'name', 'price', 'is_default'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_default' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
