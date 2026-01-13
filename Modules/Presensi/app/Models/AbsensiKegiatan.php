<?php

namespace Modules\Presensi\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AbsensiKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kegiatan_id',
        'user_id',
        'jam_absen',
        'status',
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
