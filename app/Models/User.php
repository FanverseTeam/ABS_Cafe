<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $table = 'pengguna';

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'avatar',
        'photo_path',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (User $user): void {
            if ($user->isDirty('password')) {
                $user->setRememberToken(Str::random(60));
            }

            if ($user->isDirty('status') && $user->status !== 'aktif') {
                $user->setRememberToken(Str::random(60));
            }
        });
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function primaryRole(): string
    {
        if (! empty($this->role)) {
            return $this->role;
        }

        if (method_exists($this, 'getRoleNames') && $this->getRoleNames()->isNotEmpty()) {
            return $this->getRoleNames()->first();
        }

        return 'staf';
    }

    public function roleLabel(): string
    {
        return match ($this->primaryRole()) {
            'admin' => 'Admin',
            'owner' => 'Owner',
            'kasir' => 'Kasir',
            default => 'Staf',
        };
    }

    public function photoUrl(): ?string
    {
        if ($this->avatar) {
            return $this->avatar;
        }

        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name));

        $initials = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : '?';
    }

    public function isAdmin(): bool
    {
        return $this->primaryRole() === 'admin';
    }
}
