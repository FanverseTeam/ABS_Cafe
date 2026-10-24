<?php

namespace App\Models;

use App\Support\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'kategori';

    protected $fillable = ['name', 'icon', 'order', 'description', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where('name', 'like', "%{$term}%"));
    }

    public function activityLabel(): string
    {
        return $this->name;
    }

    public function activityModule(): string
    {
        return 'Kategori Menu';
    }
}
