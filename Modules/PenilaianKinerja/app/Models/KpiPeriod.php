<?php

namespace Modules\PenilaianKinerja\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PenilaianKinerja\Database\Factories\KpiPeriodFactory;

class KpiPeriod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    public function assessments()
    {
        return $this->hasMany(PerformanceAssessment::class, 'period_id');
    }
}
