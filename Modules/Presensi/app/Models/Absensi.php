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
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLateMinutesAttribute(): int
    {
        if ($this->status !== 'hadir' || !$this->jam_masuk) {
            return 0;
        }

        $settings = \Modules\MasterData\Models\Setting::get();
        $jamMasuk = \Carbon\Carbon::parse($this->jam_masuk);
        $startTime = \Carbon\Carbon::parse($settings->office_start_time);
        $tolerance = $settings->late_tolerance ?? 0;

        // If check-in is within tolerance, it's not late
        $startTimeWithTolerance = $startTime->copy()->addMinutes($tolerance);

        if ($jamMasuk->lte($startTimeWithTolerance)) {
            return 0;
        }

        return $jamMasuk->diffInMinutes($startTime);
    }
}
