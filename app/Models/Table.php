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
        $status = $this->realtimeStatus();

        return match ($status) {
            'terpakai' => ['label' => 'Terisi', 'color' => 'danger'],
            'dipesan' => ['label' => 'Dipesan', 'color' => 'warning'],
            'nonaktif' => ['label' => 'Nonaktif', 'color' => 'muted'],
            default => ['label' => 'Kosong', 'color' => 'success'],
        };
    }

    /**
     * Reservasi yang saat ini sedang aktif menempati meja
     * (dalam rentang 1 jam sebelum mulai s/d 1 jam setelah selesai).
     */
    public function activeReservationNow(): ?Reservation
    {
        $list = $this->relationLoaded('reservations')
            ? $this->reservations->whereIn('status', ['menunggu', 'dikonfirmasi', 'datang'])
            : $this->reservations()->whereIn('status', ['menunggu', 'dikonfirmasi', 'datang'])->get();

        return $list->first(fn (Reservation $r) => $r->isTableOccupiedNow());
    }

    /**
     * Reservasi terdekat yang akan datang pada meja ini.
     */
    public function upcomingReservation(): ?Reservation
    {
        $list = $this->relationLoaded('reservations')
            ? $this->reservations->whereIn('status', ['menunggu', 'dikonfirmasi'])
            : $this->reservations()->whereIn('status', ['menunggu', 'dikonfirmasi'])->get();

        return $list->filter(fn (Reservation $r) => $r->occupiedWindowStart()->isFuture())
            ->sortBy(fn (Reservation $r) => $r->startDateTime())
            ->first();
    }

    /**
     * Resolusi status realtime meja:
     * - Jika dinonaktifkan: 'nonaktif'
     * - Jika ada pesanan aktif saat ini: 'terpakai'
     * - Jika berada dalam jendela reservasi aktif (1 jam sebelum s/d 1 jam setelah): 'terpakai'
     * - Jika belum jamnya reservasi atau sudah lewat jendela reservasi: 'tersedia' (kosong)
     */
    public function realtimeStatus(): string
    {
        if ($this->status === 'nonaktif') {
            return 'nonaktif';
        }

        // 1. Cek pesanan aktif saat ini
        if ($this->orders()->today()->active()->exists()) {
            return 'terpakai';
        }

        // 2. Cek apakah ada reservasi aktif saat ini (1 jam sebelum s/d 1 jam setelah)
        if ($this->activeReservationNow()) {
            return 'terpakai';
        }

        // 3. Jika status di DB 'dipesan' atau 'terpakai' lama tapi tidak ada pesanan aktif
        // dan tidak berada dalam jendela reservasi, meja kembali kosong
        if (in_array($this->status, ['dipesan', 'terpakai'], true)) {
            return 'tersedia';
        }

        return $this->status ?: 'tersedia';
    }
}
