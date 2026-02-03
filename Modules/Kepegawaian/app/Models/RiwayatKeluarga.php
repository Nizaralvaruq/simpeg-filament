<?php

namespace Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatKeluarga extends Model
{
    use HasFactory;

    protected $table = 'riwayat_keluargas';

    protected $fillable = [
        'data_induk_id',
        'nama',
        'hubungan',
        'pekerjaan',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'pendidikan',
        'file_kk',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function dataInduk()
    {
        return $this->belongsTo(DataInduk::class, 'data_induk_id');
    }
}
