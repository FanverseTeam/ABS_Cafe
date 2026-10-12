<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTimeline extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'status',
        'actor',
        'description',
        'logged_at',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
