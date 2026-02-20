<?php

namespace Modules\Presensi\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'status',
        'jam_masuk',
        'jam_keluar',
        'keterangan',
        'latitude',
        'longitude',
        'foto_verifikasi',
        'alamat_lokasi',
        'late_minutes',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->jam_masuk && $model->isWorkingDay($model->tanggal)) {
                $model->late_minutes = $model->calculateLateMinutes();
            } else {
                $model->late_minutes = 0;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculateLateMinutes(): int
    {
        if (!in_array($this->status, ['hadir', 'dinas_luar']) || !$this->jam_masuk) {
            return 0;
        }

        // For Dinas Luar, we don't count lateness as they might go directly to client
        if ($this->status === 'dinas_luar') {
            return 0;
        }

        if (!$this->isWorkingDay($this->tanggal)) {
            return 0;
        }

        $settings = \Modules\MasterData\Models\Setting::get();
        if (!$settings || !$settings->office_start_time) {
            return 0;
        }

        $jamMasuk = \Carbon\Carbon::parse($this->jam_masuk);
        $startTime = \Carbon\Carbon::parse($settings->office_start_time);

        // Ensure both are on the same date for accurate time-only comparison
        $startTime->setDate($jamMasuk->year, $jamMasuk->month, $jamMasuk->day);

        $tolerance = (int) ($settings->late_tolerance ?? 0);
        $startTimeWithTolerance = $startTime->copy()->addMinutes($tolerance);

        if ($jamMasuk->lte($startTimeWithTolerance)) {
            return 0;
        }

        // Return positive difference in minutes
        $diff = $startTime->diffInMinutes($jamMasuk, false);
        return (int) max(0, $diff);
    }

    public static function isWorkingDay($date = null): bool
    {
        $date = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::today();
        $settings = \Modules\MasterData\Models\Setting::get();
        $workingDays = $settings->working_days ?? [1, 2, 3, 4, 5]; // Default Mon-Fri

        return in_array($date->dayOfWeekIso, $workingDays);
    }
}
