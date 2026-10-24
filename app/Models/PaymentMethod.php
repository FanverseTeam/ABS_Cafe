<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'metode_bayar';

    protected $fillable = [
        'name',
        'code',
        'type',
        'description',
        'instructions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function icon(): string
    {
        return match ($this->code) {
            'tunai' => 'banknote',
            'qris' => 'qr-code',
            'transfer' => 'building-2',
            'ewallet' => 'smartphone',
            default => 'credit-card',
        };
    }
}
