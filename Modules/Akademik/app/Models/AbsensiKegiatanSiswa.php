<?php

namespace Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiKegiatanSiswa extends Model
{
    protected $table = 'absensi_kegiatan_siswas';

    protected $fillable = [
        'kegiatan_id',
        'siswa_id',
        'jam_absen',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'jam_absen' => 'datetime',
    ];

    /**
     * Get the kegiatan that owns the absensi.
     */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(\Modules\Presensi\Models\Kegiatan::class);
    }

    /**
     * Get the siswa that owns the absensi.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
