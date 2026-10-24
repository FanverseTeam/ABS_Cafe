<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMovement extends Kas
{
    use HasFactory;

    protected $table = 'kas';

    protected static function booted(): void
    {
        static::addGlobalScope('movement', function (Builder $builder) {
            $builder->whereIn('type', ['masuk', 'keluar']);
        });

        static::creating(function ($model) {
            if (empty($model->type)) {
                $model->type = 'keluar';
            }
        });
    }

    public function getCashShiftIdAttribute()
    {
        return $this->attributes['shift_id'] ?? null;
    }

    public function setCashShiftIdAttribute($value): void
    {
        $this->attributes['shift_id'] = $value;
    }

    public function cashShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class, 'shift_id');
    }
}
