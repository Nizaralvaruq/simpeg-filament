<?php

namespace Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Akademik\Database\Factories\SiswaFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int         $id
 * @property string      $nis
 * @property string      $nama_lengkap
 * @property string|null $kelas
 * @property string|null $nomor_wa_ortu
 * @property bool        $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nis',
        'nama_lengkap',
        'kelas',
        'nomor_wa_ortu',
        'is_active',
    ];

    public function setoranNgajis()
    {
        return $this->hasMany(SetoranNgaji::class);
    }
}
