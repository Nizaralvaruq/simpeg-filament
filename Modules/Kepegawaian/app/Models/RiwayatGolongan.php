<?php

namespace Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MasterData\Models\Golongan;

// use Modules\Kepegawaian\Database\Factories\RiwayatGolonganFactory;

class RiwayatGolongan extends Model
{
    protected $table = 'riwayat_golongans';

    protected $fillable = [
        'data_induk_id',
        'tanggal',
        'golongan_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function dataInduk()
    {
        return $this->belongsTo(DataInduk::class, 'data_induk_id');
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class, 'golongan_id');
    }
}
