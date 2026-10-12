<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashShift extends Model
{
    protected $fillable = [
        'user_id',
        'opened_at',
        'closed_at',
        'opening_cash',
        'expected_cash',
        'closing_cash',
        'cash_difference',
        'total_sales',
        'total_transactions',
        'status',
        'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_transactions' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'cash_shift_id');
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function durationLabel(): string
    {
        if (! $this->opened_at || ! $this->closed_at) {
            return $this->opened_at ? 'Sedang berjalan' : '-';
        }

        $minutes = $this->opened_at->diffInMinutes($this->closed_at);
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return $hours > 0 ? "{$hours} jam {$mins} mnt" : "{$mins} menit";
    }

    /**
     * @return array{label: string, color: string}
     */
    public function statusBadge(): array
    {
        return $this->status === 'tutup'
            ? ['label' => 'Ditutup', 'color' => 'muted']
            : ['label' => 'Berjalan', 'color' => 'success'];
    }

    /**
     * @return array{label: string, color: string}
     */
    public function differenceBadge(): array
    {
        $diff = (float) $this->cash_difference;

        if (abs($diff) < 1) {
            return ['label' => 'Sesuai', 'color' => 'success'];
        }

        return $diff > 0
            ? ['label' => 'Surplus', 'color' => 'warning']
            : ['label' => 'Kurang', 'color' => 'danger'];
    }

    public function scopeInPeriod($query, Carbon $start, Carbon $end)
    {
        return $query->whereBetween('opened_at', [$start, $end]);
    }
}
