<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'reason',
        'applied_to_shift_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appliedShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class, 'applied_to_shift_id');
    }
}
