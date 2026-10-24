<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    protected $table = 'meja';

    protected $fillable = [
        'number',
        'capacity',
        'status',
        'location_notes',
        'area',
        'notes',
    ];

    protected $casts = [
        'number' => 'integer',
        'capacity' => 'integer',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_number', 'number');
    }

    public function statusBadge(): array
    {
        return match ($this->status) {
            'terpakai' => ['label' => 'Terisi', 'color' => 'danger'],
            'dipesan' => ['label' => 'Dipesan', 'color' => 'warning'],
            'nonaktif' => ['label' => 'Nonaktif', 'color' => 'muted'],
            default => ['label' => 'Kosong', 'color' => 'success'],
        };
    }
}
