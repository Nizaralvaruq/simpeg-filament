<?php
// Forced re-index 2026-02-03

namespace Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatPendidikan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pendidikans';

    protected $fillable = [
        'data_induk_id',
        'jenjang',
        'institusi',
        'jurusan',
        'tahun_lulus',
        'file_ijazah',
    ];

    public function dataInduk()
    {
        return $this->belongsTo(DataInduk::class, 'data_induk_id');
    }
}
