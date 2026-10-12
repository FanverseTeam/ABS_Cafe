<?php

namespace App\Models;

use App\Support\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'unit', 'quantity', 'min_quantity', 'is_active'];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'min_quantity' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)->withTimestamps();
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where('name', 'like', "%{$term}%"));
    }

    /**
     * @return array{label: string, color: string}
     */
    public function stockStatus(): array
    {
        if ((float) $this->quantity <= 0) {
            return ['label' => 'Habis', 'color' => 'danger'];
        }

        if ((float) $this->quantity <= (float) $this->min_quantity) {
            return ['label' => 'Menipis', 'color' => 'warning'];
        }

        return ['label' => 'Aman', 'color' => 'success'];
    }

    public function activityLabel(): string
    {
        return $this->name;
    }

    public function activityModule(): string
    {
        return 'Stok';
    }
}
