<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'customer_name',
        'table_number',
        'order_type',
        'subtotal',
        'tax',
        'service_charge',
        'discount',
        'promo_id',
        'total',
        'payment_method',
        'payment_status',
        'order_status',
        'cashier_name',
        'cashier_id',
        'cash_shift_id',
        'paid_at',
        'cancel_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'table_number' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(OrderTimeline::class)->orderBy('logged_at', 'asc');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function cashShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class, 'cash_shift_id');
    }

    /**
     * Nama kasir efektif (relasi user atau kolom teks lama).
     */
    public function cashierLabel(): string
    {
        return $this->cashier?->name ?: ($this->cashier_name ?: 'Belum ditugaskan');
    }
}
