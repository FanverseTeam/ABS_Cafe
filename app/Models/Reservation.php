<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    public const STATUSES = [
        'menunggu' => 'Menunggu',
        'dikonfirmasi' => 'Dikonfirmasi',
        'datang' => 'Datang',
        'selesai' => 'Selesai',
        'batal' => 'Batal',
        'tidak_datang' => 'Tidak Datang',
    ];

    protected $fillable = [
        'code',
        'customer_name',
        'phone',
        'party_size',
        'reservation_date',
        'reservation_time',
        'table_id',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'party_size' => 'integer',
        'reservation_date' => 'date',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    /**
     * @return array{label: string, color: string}
     */
    public function statusBadge(): array
    {
        return match ($this->status) {
            'menunggu' => ['label' => 'Menunggu', 'color' => 'warning'],
            'dikonfirmasi' => ['label' => 'Dikonfirmasi', 'color' => 'info'],
            'datang' => ['label' => 'Datang', 'color' => 'success'],
            'selesai' => ['label' => 'Selesai', 'color' => 'muted'],
            'batal' => ['label' => 'Batal', 'color' => 'danger'],
            'tidak_datang' => ['label' => 'Tidak Datang', 'color' => 'danger'],
            default => ['label' => ucfirst($this->status), 'color' => 'muted'],
        };
    }

    public function datetimeLabel(): string
    {
        return $this->reservation_date->translatedFormat('d M Y').' '.substr((string) $this->reservation_time, 0, 5);
    }

    public static function generateCode(): string
    {
        $prefix = 'RSV-'.now()->format('ymd').'-';
        $last = static::where('code', 'like', $prefix.'%')->orderByDesc('id')->value('code');
        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }
}
