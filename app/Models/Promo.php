<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'description',
        'type',
        'value',
        'applies_to',
        'target_category_id',
        'start_date',
        'end_date',
        'banner',
        'is_active',
        'min_spend',
        'quota',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'quota' => 'integer',
    ];

    public function targetCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'target_category_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getStatusLabelAttribute(): string
    {
        $today = now()->startOfDay();
        if (! $this->is_active) {
            return 'nonaktif';
        }
        if ($today->lt($this->start_date)) {
            return 'terjadwal';
        }
        if ($today->gt($this->end_date)) {
            return 'berakhir';
        }

        return 'berjalan';
    }

    public function scopeRunning(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }

    public function isRunning(): bool
    {
        return $this->is_active
            && $this->start_date <= now()->startOfDay()
            && $this->end_date >= now()->startOfDay();
    }

    public function usedCount(): int
    {
        return $this->orders()->where('payment_status', 'lunas')->count();
    }

    public function remainingQuota(): ?int
    {
        if ($this->quota === null) {
            return null;
        }

        return max(0, $this->quota - $this->usedCount());
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'persen' => $this->value.'%',
            'nominal' => 'Rp '.number_format((float) $this->value, 0, ',', '.'),
            'buy_x_get_y' => 'Beli 2 Gratis 1',
            default => ucfirst((string) $this->type),
        };
    }
}
