<?php

namespace Modules\Presensi\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class JadwalPiket extends Model
{
    protected $table = 'jadwal_piket';

    protected $fillable = [
        'user_id',
        'tanggal',
        'shift',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relationship: User yang dijadwalkan piket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: User yang membuat jadwal
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Jadwal piket hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    /**
     * Scope: Jadwal piket untuk tanggal tertentu
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    /**
     * Scope: Jadwal piket untuk user tertentu
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Jadwal aktif (hari ini atau masa depan)
     */
    public function scopeActive($query)
    {
        return $query->whereDate('tanggal', '>=', Carbon::today());
    }

    /**
     * Check apakah user sedang piket hari ini
     */
    public static function isUserOnPiketToday(int $userId): bool
    {
        return static::query()
            ->where('user_id', $userId)
            ->today()
            ->exists();
    }

    /**
     * Get semua petugas piket hari ini
     */
    public static function getTodayPiketOfficers()
    {
        return static::query()
            ->with('user')
            ->today()
            ->get()
            ->pluck('user');
    }

    /**
     * Get jumlah petugas piket hari ini
     */
    public static function getTodayPiketCount(): int
    {
        return static::query()->today()->count();
    }
}
