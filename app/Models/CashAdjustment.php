<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashAdjustment extends Kas
{
    use HasFactory;

    protected $table = 'kas';

    protected static function booted(): void
    {
        static::addGlobalScope('adjustment', function (Builder $builder) {
            $builder->where('type', 'penyesuaian');
        });

        static::creating(function ($model) {
            $model->type = 'penyesuaian';
        });
    }

    public function getAppliedToShiftIdAttribute()
    {
        return $this->attributes['shift_id'] ?? null;
    }

    public function setAppliedToShiftIdAttribute($value): void
    {
        $this->attributes['shift_id'] = $value;
    }

    public function appliedShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class, 'shift_id');
    }
}
