<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kas extends Model
{
    use HasFactory;

    protected $table = 'kas';

    protected $fillable = [
        'shift_id',
        'user_id',
        'type',
        'amount',
        'reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class, 'shift_id');
    }

    public function cashShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class, 'shift_id');
    }

    public function appliedShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class, 'shift_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeMovements(Builder $query): Builder
    {
        return $query->whereIn('type', ['masuk', 'keluar']);
    }

    public function scopeAdjustments(Builder $query): Builder
    {
        return $query->where('type', 'penyesuaian');
    }

    public function scopeUnapplied(Builder $query): Builder
    {
        return $query->where('type', 'penyesuaian')->whereNull('shift_id');
    }

    public function scopeIn(Builder $query): Builder
    {
        return $query->where('type', 'masuk');
    }

    public function scopeOut(Builder $query): Builder
    {
        return $query->where('type', 'keluar');
    }

    public function isAdjustment(): bool
    {
        return $this->type === 'penyesuaian';
    }

    public function isMovement(): bool
    {
        return in_array($this->type, ['masuk', 'keluar'], true);
    }

    public function typeBadge(): array
    {
        return match ($this->type) {
            'masuk' => ['label' => 'Kas Masuk', 'color' => 'success'],
            'keluar' => ['label' => 'Kas Keluar', 'color' => 'danger'],
            default => ['label' => 'Penyesuaian', 'color' => 'warning'],
        };
    }
}
