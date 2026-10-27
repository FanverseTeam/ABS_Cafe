<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuVariant extends Model
{
    use HasFactory;

    protected $table = 'menu_varian';

    protected $fillable = [
        'menu_id', 'name', 'size', 'price', 'is_default', 'is_available', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_default' => 'boolean',
            'is_available' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function displayName(): string
    {
        return trim($this->name.($this->size ? ' - '.$this->size : ''));
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'menu_variant_id');
    }

    public function isHabis(): bool
    {
        return ! $this->is_available;
    }

    public function getIsHabisAttribute(): bool
    {
        return $this->isHabis();
    }
}
