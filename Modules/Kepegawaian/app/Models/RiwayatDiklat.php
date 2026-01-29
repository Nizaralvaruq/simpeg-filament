<?php

namespace Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatDiklat extends Model
{
    use HasFactory;

    protected $table = 'riwayat_diklats';

    protected $fillable = [
        'data_induk_id',
        'nama_diklat',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_jam',
        'file_sertifikat',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function dataInduk()
    {
        return $this->belongsTo(DataInduk::class, 'data_induk_id');
    }
}
