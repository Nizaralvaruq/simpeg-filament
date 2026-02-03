<?php
// Forced re-index 2026-02-03

namespace Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatPenghargaan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_penghargaans';

    protected $fillable = [
        'data_induk_id',
        'nama_penghargaan',
        'pemberi',
        'tanggal',
        'file_sertifikat',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function dataInduk()
    {
        return $this->belongsTo(DataInduk::class, 'data_induk_id');
    }
}
