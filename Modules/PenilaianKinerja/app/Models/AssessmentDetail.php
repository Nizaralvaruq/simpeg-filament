<?php

namespace Modules\PenilaianKinerja\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PenilaianKinerja\Database\Factories\AssessmentDetailFactory;

class AssessmentDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['performance_assessment_id', 'kpi_indicator_id', 'score', 'note'];

    public function assessment()
    {
        return $this->belongsTo(PerformanceAssessment::class, 'performance_assessment_id');
    }

    public function indicator()
    {
        return $this->belongsTo(KpiIndicator::class, 'kpi_indicator_id');
    }
}
