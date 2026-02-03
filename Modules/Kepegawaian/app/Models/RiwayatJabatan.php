<?php

namespace Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Kepegawaian\Database\Factories\RiwayatJabatanFactory;

class RiwayatJabatan extends Model
{
    protected $table = 'riwayat_jabatans';

    protected $fillable = [
        'data_induk_id',
        'tanggal',
        'nama_jabatan',
        'unit_id',
        'nomor_sk',
        'file_sk',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function dataInduk()
    {
        return $this->belongsTo(DataInduk::class, 'data_induk_id');
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\MasterData\Models\Unit::class, 'unit_id');
    }
}
