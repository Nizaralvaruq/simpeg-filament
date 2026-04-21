<?php

namespace Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Akademik\Database\Factories\SetoranNgajiFactory;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class SetoranNgaji extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'siswa_id',
        'guru_id',
        'tanggal_setoran',
        'jenis_setoran',
        'nama_materi',
        'ayat_halaman',
        'predikat_nilai',
        'catatan_guru',
        'status_notifikasi',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
