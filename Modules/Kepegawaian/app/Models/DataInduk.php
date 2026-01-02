<?php

namespace Modules\Kepegawaian\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\MasterData\Models\Unit;
use Modules\MasterData\Models\Golongan;

class DataInduk extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
    
}
