<?php

namespace Modules\Presensi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kegiatan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'keterangan',
        'is_wajib',
        'is_closed',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_wajib' => 'boolean',
        'is_closed' => 'boolean',
    ];

    public function absensiKegiatans(): HasMany
    {
        return $this->hasMany(AbsensiKegiatan::class);
    }
}
