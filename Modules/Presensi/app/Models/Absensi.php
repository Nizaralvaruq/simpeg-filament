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
        'uraian_harian',
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

        $jamMasuk = \Carbon\Carbon::parse($this->jam_masuk);
        $startTime = \Carbon\Carbon::parse('07:00:00');

        if ($jamMasuk->lte($startTime)) {
            return 0;
        }

        return $jamMasuk->diffInMinutes($startTime);
    }
}
