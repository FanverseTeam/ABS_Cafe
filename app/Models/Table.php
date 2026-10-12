<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'number',
        'capacity',
        'status',
        'location_notes',
    ];

    protected $casts = [
        'number' => 'integer',
        'capacity' => 'integer',
    ];
}
