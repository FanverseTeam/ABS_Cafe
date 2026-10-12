<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'total',
        'payment_method',
        'payment_status',
        'order_status',
        'cashier_name',
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
}
