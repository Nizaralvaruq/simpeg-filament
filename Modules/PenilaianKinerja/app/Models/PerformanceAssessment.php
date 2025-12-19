<?php

namespace Modules\PenilaianKinerja\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\PenilaianKinerja\Database\Factories\PerformanceAssessmentFactory;

use Modules\Kepegawaian\Models\DataInduk;
use App\Models\User;

class PerformanceAssessment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'data_induk_id',
        'period_id',
        'assessor_id',
        'total_score',
        'status',
        'comment'
    ];

    public function employee()
    {
        return $this->belongsTo(DataInduk::class, 'data_induk_id');
    }

    public function period()
    {
        return $this->belongsTo(KpiPeriod::class, 'period_id');
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function details()
    {
        return $this->hasMany(AssessmentDetail::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (PerformanceAssessment $assessment) {
            // Calculation only if details are loaded or manually triggered
            // Usually details are saved after the header, so this might need a 'saved' listener 
            // and an update, but for simplified logic we can calculate if details exist.
        });
    }

    public function calculateTotalScore(): void
    {
        $totalWeight = 0;
        $earnedScore = 0;

        $this->loadMissing('details.indicator.category');

        $categories = $this->details->groupBy('indicator.kpi_category_id');

        foreach ($categories as $categoryId => $details) {
            $categoryWeight = $details->first()->indicator->category->weight ?? 0;
            $categoryEarned = 0;
            $categoryMax = 0;

            foreach ($details as $detail) {
                $categoryEarned += $detail->score;
                $categoryMax += $detail->indicator->max_score;
            }

            if ($categoryMax > 0) {
                $earnedScore += ($categoryEarned / $categoryMax) * $categoryWeight;
            }
        }

        $this->total_score = $earnedScore;
    }
}
