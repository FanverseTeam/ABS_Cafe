<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'order_id',
        'cashier_id',
        'method',
        'amount',
        'cash_received',
        'change_given',
        'reference',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_given' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'tunai' => 'Tunai',
            'qris' => 'QRIS',
            'debit' => 'Kartu Debit',
            default => ucfirst((string) $this->method),
        };
    }
}
