<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjagaKasir extends Model
{
    use HasFactory;

    protected $table = 'penjaga_kasir';

    protected $fillable = [
        'name',
        'phone',
        'status',
    ];

    /**
     * Scope untuk mengambil penjaga kasir dengan status aktif.
     *
     * @param  Builder<PenjagaKasir>  $query
     * @return Builder<PenjagaKasir>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'aktif')->orderBy('name');
    }

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    public function getUsernameAttribute(): string
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $this->name));
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $first = isset($words[0]) && $words[0] !== '' ? mb_substr($words[0], 0, 1) : 'K';
        $second = isset($words[1]) && $words[1] !== '' ? mb_substr($words[1], 0, 1) : '';

        return mb_strtoupper($first.$second);
    }
}
