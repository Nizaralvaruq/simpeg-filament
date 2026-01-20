<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Modules\Kepegawaian\Models\DataInduk;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /**
     * @mixin \Spatie\Permission\Traits\HasRoles
     */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole(['super_admin', 'kepala_sekolah', 'koor_jenjang', 'admin_unit', 'staff', 'ketua_psdm']);
        }



        return false;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar_url',
        'password',
        'qr_token',
        'qr_token_generated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'qr_token_generated_at' => 'datetime',
        ];
    }

    /**
     * Dynamic QR Token accessor using NIP/NPA as fallback
     */
    public function getQrTokenAttribute(): ?string
    {
        return $this->attributes['qr_token'] ?? $this->employee?->nip;
    }

    public function generateQrToken(): void
    {
        $this->update([
            'qr_token' => \Illuminate\Support\Str::random(32),
            'qr_token_generated_at' => now(),
        ]);
    }


    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function employee()
    {
        return $this->hasOne(DataInduk::class, 'user_id');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (!$this->avatar_url) {
            return null;
        }

        return asset('storage/' . $this->avatar_url);
    }
}
