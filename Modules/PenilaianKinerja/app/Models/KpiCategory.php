<?php

namespace Modules\PenilaianKinerja\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PenilaianKinerja\Database\Factories\KpiCategoryFactory;

class KpiCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'weight'];

    public function indicators()
    {
        return $this->hasMany(KpiIndicator::class);
    }
}
