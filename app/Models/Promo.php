<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promo extends Model
{
    protected $fillable = [
        'title',
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
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function targetCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'target_category_id');
    }

    public function getStatusLabelAttribute(): string
    {
        $today = now()->startOfDay();
        if (!$this->is_active) {
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
}
