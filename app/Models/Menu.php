<?php

namespace App\Models;

use App\Support\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Menu extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'menu';

    protected $fillable = [
        'category_id', 'name', 'variant_group', 'variant_label',
        'description', 'price', 'image',
        'is_available', 'is_best_seller', 'sold_count',
        'merged_into_menu_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_best_seller' => 'boolean',
            'sold_count' => 'integer',
            'merged_into_menu_id' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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

    // ─── Variant group helpers ─────────────────────────────────────────────

    /**
     * Returns all menu rows that share the same variant_group (including self),
     * ordered by variant_label. Returns a collection containing only self
     * when this menu has no variant_group.
     *
     * @return Collection<int, static>
     */
    public function groupSiblings(): Collection
    {
        if (! $this->variant_group) {
            return collect([$this]);
        }

        return static::where('variant_group', $this->variant_group)
            ->orderBy('variant_label')
            ->get();
    }

    /**
     * Whether this menu belongs to a visual variant group.
     */
    public function hasVariantGroup(): bool
    {
        return filled($this->variant_group);
    }

    // ─── Availability ──────────────────────────────────────────────────────

    public function isHabis(): bool
    {
        return ! $this->is_available;
    }

    public function getIsHabisAttribute(): bool
    {
        return $this->isHabis();
    }

    // ─── Display helpers ───────────────────────────────────────────────────

    public function imageUrl(): ?string
    {
        return $this->image ?: null;
    }

    public function getGambarAttribute(): ?string
    {
        return $this->imageUrl();
    }

    /**
     * Price label shown to customers.
     * When part of a group, shows the price of this specific item.
     */
    public function customerPriceLabel(): string
    {
        return 'Rp '.number_format((float) $this->price, 0, ',', '.');
    }

    /**
     * Display name used in order lines.
     * Returns the full menu name (which already includes the variant label).
     */
    public function orderLineName(): string
    {
        return $this->name;
    }

    public function variantNameList(): string
    {
        if ($this->variant_group) {
            $siblings = $this->groupSiblings();

            return $siblings->pluck('variant_label')->filter()->implode(' / ');
        }

        return $this->variant_label ?: '';
    }

    public function priceRangeLabel(): string
    {
        if ($this->variant_group) {
            $siblings = $this->groupSiblings();
            $prices = $siblings->pluck('price');
            $min = (float) $prices->min();
            $max = (float) $prices->max();

            if ($min !== $max && $max > 0) {
                return 'Rp '.number_format($min, 0, ',', '.').' - Rp '.number_format($max, 0, ',', '.');
            }
        }

        return 'Rp '.number_format((float) $this->price, 0, ',', '.');
    }

    /**
     * Base name without the parenthetical label, e.g. "Latte" from "Latte (Ice)".
     * Falls back to variant_group if set, otherwise returns the full name.
     */
    public function variantBaseName(): string
    {
        if ($this->variant_group) {
            return $this->variant_group;
        }

        return trim((string) preg_replace('/\s*\([^)]*\)\s*$/', '', $this->name));
    }

    // ─── Scopes ────────────────────────────────────────────────────────────

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%"));
    }

    /**
     * Scope to get only the "primary" representative of each variant group,
     * useful for listing menus without duplicates when grouping in PHP/JS.
     * Returns menus without a group + the first (alphabetically) of each group.
     */
    public function scopeGroupRepresentative(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('variant_group')
                ->orWhereRaw('id IN (
                    SELECT MIN(id) FROM menu
                    WHERE variant_group IS NOT NULL
                    AND deleted_at IS NULL
                    GROUP BY variant_group
                )');
        });
    }

    // ─── Activity log ──────────────────────────────────────────────────────

    public function activityLabel(): string
    {
        return $this->name;
    }

    public function activityModule(): string
    {
        return 'Menu';
    }
}
