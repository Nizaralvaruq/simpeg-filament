<?php

namespace Modules\PenilaianKinerja\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PenilaianKinerja\Database\Factories\KpiIndicatorFactory;

class KpiIndicator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['kpi_category_id', 'name', 'description', 'max_score'];

    public function category()
    {
        return $this->belongsTo(KpiCategory::class, 'kpi_category_id');
    }
}
