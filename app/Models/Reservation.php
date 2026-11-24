<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservasi';

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
        'reservation_end_date',
        'reservation_end_time',
        'table_id',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'party_size' => 'integer',
        'reservation_date' => 'date',
        'reservation_end_date' => 'date',
    ];

    public function startDateTime(): Carbon
    {
        $dateStr = $this->reservation_date ? $this->reservation_date->format('Y-m-d') : today()->toDateString();
        $timeStr = $this->reservation_time ? substr((string) $this->reservation_time, 0, 5) : '00:00';

        return Carbon::parse("{$dateStr} {$timeStr}");
    }

    public function endDateTime(): Carbon
    {
        $startDate = $this->reservation_date ? $this->reservation_date->format('Y-m-d') : today()->toDateString();
        $endDate = $this->reservation_end_date ? $this->reservation_end_date->format('Y-m-d') : $startDate;

        if ($this->reservation_end_time) {
            $endTime = substr((string) $this->reservation_end_time, 0, 5);
            $end = Carbon::parse("{$endDate} {$endTime}");
        } else {
            return $this->startDateTime()->addHours(2);
        }

        return $end->greaterThanOrEqualTo($this->startDateTime()) ? $end : $this->startDateTime()->addHours(2);
    }

    /**
     * Waktu mulai meja dianggap terisi (1 jam sebelum jam mulai reservasi).
     */
    public function occupiedWindowStart(): Carbon
    {
        return $this->startDateTime()->copy()->subHour();
    }

    /**
     * Waktu selesai meja dianggap terisi (1 jam setelah jam selesai reservasi).
     */
    public function occupiedWindowEnd(): Carbon
    {
        return $this->endDateTime()->copy()->addHour();
    }

    /**
     * Cek apakah saat ini meja terisi oleh reservasi ini (1 jam sebelum mulai s/d 1 jam setelah selesai).
     */
    public function isTableOccupiedNow(): bool
    {
        if (in_array($this->status, ['batal', 'tidak_datang', 'selesai'], true)) {
            return false;
        }

        return now()->between($this->occupiedWindowStart(), $this->occupiedWindowEnd());
    }

    public function datetimeRangeLabel(): string
    {
        $start = $this->startDateTime();
        $end = $this->endDateTime();

        if ($start->isSameDay($end)) {
            return $start->translatedFormat('d M Y').', '.$start->format('H:i').' - '.$end->format('H:i');
        }

        return $start->translatedFormat('d M Y H:i').' s/d '.$end->translatedFormat('d M Y H:i');
    }

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
