<?php

namespace App\Models;

use App\Support\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'menu';

    protected $fillable = [
        'category_id', 'name', 'description', 'price', 'image',
        'is_available', 'is_best_seller', 'stock', 'min_stock', 'unit', 'sold_count',
        'merged_into_menu_id', 'variant_id',
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
            'merged_into_menu_id' => 'integer',
            'variant_id' => 'integer',
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
        if (! $this->is_available) {
            return 'habis';
        }

        if ($this->stock !== null) {
            if ($this->stock <= 0) {
                return 'habis';
            }

            if ($this->min_stock !== null && $this->stock <= $this->min_stock) {
                return 'menipis';
            }
        }

        if ($this->relationLoaded('recipes') ? $this->recipes->isNotEmpty() : $this->recipes()->exists()) {
            $recipes = $this->relationLoaded('recipes') ? $this->recipes : $this->recipes()->with('stock')->get();
            foreach ($recipes as $r) {
                if ($r->stock) {
                    if ((float) $r->stock->quantity <= 0) {
                        return 'habis';
                    }
                    if ((float) $r->stock->min_quantity > 0 && (float) $r->stock->quantity <= (float) $r->stock->min_quantity) {
                        return 'menipis';
                    }
                }
            }
        }

        return 'aman';
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MenuVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    public function availableVariants(): HasMany
    {
        return $this->variants()->where('is_available', true);
    }

    public function mergedInto(): BelongsTo
    {
        return $this->belongsTo(self::class, 'merged_into_menu_id');
    }

    public function mergedFrom(): HasMany
    {
        return $this->hasMany(self::class, 'merged_into_menu_id')->withTrashed();
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function imageUrl(): ?string
    {
        return $this->image ?: null;
    }

    public function getGambarAttribute(): ?string
    {
        return $this->imageUrl();
    }

    public function isHabis(): bool
    {
        if (! $this->is_available) {
            return true;
        }

        if ($this->relationLoaded('variants') ? $this->variants->isNotEmpty() : $this->variants()->exists()) {
            $variants = $this->relationLoaded('variants') ? $this->variants : $this->variants()->get();
            $availableVariants = $variants->where('is_available', true);
            if ($availableVariants->isEmpty()) {
                return true;
            }
        }

        return $this->stock_status === 'habis';
    }

    public function getIsHabisAttribute(): bool
    {
        return $this->isHabis();
    }

    public function customerPriceLabel(): string
    {
        $variants = $this->relationLoaded('variants') ? $this->variants : $this->variants()->get();
        $availableVariants = $variants->where('is_available', true);
        $activeVariants = $availableVariants->isNotEmpty() ? $availableVariants : $variants;

        if ($activeVariants->isEmpty()) {
            return 'Rp '.number_format((float) $this->price, 0, ',', '.');
        }

        $minPrice = (float) $activeVariants->min('price');

        if ($variants->count() > 1) {
            return 'Mulai Rp '.number_format($minPrice, 0, ',', '.');
        }

        return 'Rp '.number_format($minPrice, 0, ',', '.');
    }

    public function hasMultipleVariants(): bool
    {
        return $this->variants()->count() > 1;
    }

    /**
     * Nama dasar untuk menu hasil gabungan, tanpa akhiran "(Hot / Ice)".
     */
    public function variantBaseName(): string
    {
        return trim((string) preg_replace('/\s*\([^)]*\/[^)]*\)\s*$/', '', $this->name));
    }

    public function variantNameList(): string
    {
        $names = $this->relationLoaded('variants')
            ? $this->variants->pluck('name')
            : $this->variants()->pluck('name');

        return $names->unique()->filter()->implode(' / ');
    }

    public function priceRangeLabel(): string
    {
        $prices = $this->relationLoaded('variants')
            ? $this->variants->pluck('price')
            : $this->variants()->pluck('price');

        if ($prices->isEmpty()) {
            return 'Rp '.number_format((float) $this->price, 0, ',', '.');
        }

        $min = (float) $prices->min();
        $max = (float) $prices->max();

        if ($min === $max) {
            return 'Rp '.number_format($min, 0, ',', '.');
        }

        return 'Rp '.number_format($min, 0, ',', '.').' - Rp '.number_format($max, 0, ',', '.');
    }

    public function orderLineName(?MenuVariant $variant): string
    {
        if (! $variant) {
            return $this->name;
        }

        $variantCount = $this->relationLoaded('variants')
            ? $this->variants->count()
            : $this->variants()->count();

        if ($variantCount > 1 || str_contains($this->name, ' / ')) {
            return trim($this->variantBaseName().' - '.$variant->name.($variant->size ? ' - '.$variant->size : ''));
        }

        if ($variant->name === 'Porsi' && ! $variant->size) {
            return $this->name;
        }

        if (str_contains($this->name, $variant->name)) {
            return $variant->size ? $this->name.' - '.$variant->size : $this->name;
        }

        return $this->name.' ('.$variant->displayName().')';
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
