<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_name',
        'role',
        'activity',
        'activity_type',
        'ip_address',
    ];
}
