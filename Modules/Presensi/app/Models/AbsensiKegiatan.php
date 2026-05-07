<?php

namespace Modules\Presensi\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $kegiatan_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $jam_absen
 * @property string $status
 * @property string $metode_scan
 * @property string|null $keterangan
 * @property-read Kegiatan $kegiatan
 * @property-read \App\Models\User $user
 */
class AbsensiKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kegiatan_id',
        'user_id',
        'jam_absen',
        'status',
        'metode_scan',
        'keterangan',
    ];

    protected $casts = [
        'jam_absen' => 'datetime',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
