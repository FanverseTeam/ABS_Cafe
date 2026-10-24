<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'pesanan_item';

    protected $fillable = [
        'order_id',
        'menu_id',
        'variant_id',
        'name',
        'price',
        'quantity',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MenuVariant::class, 'variant_id');
    }
}
