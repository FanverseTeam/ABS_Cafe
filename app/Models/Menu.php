<?php

namespace App\Models;

use App\Support\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use LogsActivity;

    protected $fillable = [
        'category_id', 'name', 'description', 'price', 'image',
        'is_available', 'is_best_seller', 'stock', 'min_stock', 'unit', 'sold_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_best_seller' => 'boolean',
            'stock' => 'integer',
            'min_stock' => 'integer',
            'sold_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Status stok untuk tampilan & filter: aman, menipis, habis.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock === null) {
            return 'aman';
        }

        if ($this->stock <= 0) {
            return 'habis';
        }

        if ($this->min_stock !== null && $this->stock <= $this->min_stock) {
            return 'menipis';
        }

        return 'aman';
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MenuVariant::class);
    }

    public function imageUrl(): ?string
    {
        return $this->image ?: null;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%"));
    }

    public function activityLabel(): string
    {
        return $this->name;
    }

    public function activityModule(): string
    {
        return 'Menu';
    }
}
