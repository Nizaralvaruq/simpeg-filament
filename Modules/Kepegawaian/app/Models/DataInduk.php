<?php

namespace Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\MasterData\Models\Unit;
use Modules\MasterData\Models\Golongan;
use Modules\Kepegawaian\Models\RiwayatJabatan;
use Modules\Kepegawaian\Models\RiwayatGolongan;
use Modules\Kepegawaian\Models\RiwayatPendidikan;
use Modules\Kepegawaian\Models\RiwayatDiklat;
use Modules\Kepegawaian\Models\RiwayatPenghargaan;
use Modules\Kepegawaian\Models\RiwayatKeluarga;

class DataInduk extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::deleting(function ($dataInduk) {
            if ($dataInduk->user) {
                $dataInduk->user->delete();
            }
        });
    }

    protected $guarded = [];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_awal' => 'date',
        'tmt_akhir' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'employee_unit', 'employee_id', 'unit_id');
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class, 'golongan_id');
    }

    public function riwayatJabatans()
    {
        return $this->hasMany(RiwayatJabatan::class, 'data_induk_id');
    }

    public function riwayatGolongans()
    {
        return $this->hasMany(RiwayatGolongan::class, 'data_induk_id');
    }

    public function absensis()
    {
        return $this->hasMany(\Modules\Presensi\Models\Absensi::class, 'user_id', 'user_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function riwayatPendidikans()
    {
        return $this->hasMany(RiwayatPendidikan::class, 'data_induk_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function riwayatDiklats()
    {
        return $this->hasMany(RiwayatDiklat::class, 'data_induk_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function riwayatPenghargaans()
    {
        return $this->hasMany(RiwayatPenghargaan::class, 'data_induk_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function riwayatKeluargas()
    {
        return $this->hasMany(RiwayatKeluarga::class, 'data_induk_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function riwayatPasangan()
    {
        return $this->hasMany(RiwayatKeluarga::class, 'data_induk_id')
            ->whereIn('hubungan', ['Suami', 'Istri']);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany */
    public function riwayatAnaks()
    {
        return $this->hasMany(RiwayatKeluarga::class, 'data_induk_id')
            ->where('hubungan', 'Anak');
    }
}
// Cache-buster: re-indexing trigger 2026-02-03
