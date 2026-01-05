<?php

namespace Modules\PenilaianKinerja\Models;

use App\Models\User;
use Modules\Kepegawaian\Models\DataInduk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppraisalAssignment extends Model
{
    protected $guarded = [];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AppraisalSession::class, 'session_id');
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function ratee(): BelongsTo
    {
        return $this->belongsTo(DataInduk::class, 'ratee_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(AppraisalResult::class, 'assignment_id');
    }

    public static function getAggregatedReport($sessionId, $rateeId)
    {
        $assignments = self::where('session_id', $sessionId)
            ->where('ratee_id', $rateeId)
            ->where('status', 'completed')
            ->with(['results.indicator.category'])
            ->get();

        if ($assignments->isEmpty()) {
            return null;
        }

        $session = AppraisalSession::find($sessionId);
        $weights = [
            'superior' => $session->superior_weight ?? 50,
            'peer' => $session->peer_weight ?? 30,
            'self' => $session->self_weight ?? 20,
        ];

        $scoresByType = [
            'superior' => [],
            'peer' => [],
            'self' => [],
        ];

        foreach ($assignments as $assignment) {
            $totalIndicatorScore = $assignment->results->avg('score');
            if ($totalIndicatorScore) {
                $scoresByType[$assignment->relation_type][] = $totalIndicatorScore;
            }
        }

        $finalScore = 0;
        $totalAvailableWeight = 0;

        foreach ($scoresByType as $type => $scores) {
            if (!empty($scores)) {
                $avgForType = array_sum($scores) / count($scores);
                $finalScore += $avgForType * ($weights[$type] / 100);
                $totalAvailableWeight += $weights[$type];
            }
        }

        // Normalize if some types are missing
        if ($totalAvailableWeight > 0 && $totalAvailableWeight < 100) {
            $finalScore = ($finalScore / $totalAvailableWeight) * 100;
        }

        return round($finalScore, 2);
    }

    public static function getCategoryReport($sessionId, $rateeId)
    {
        $assignments = self::where('session_id', $sessionId)
            ->where('ratee_id', $rateeId)
            ->where('status', 'completed')
            ->with(['results.indicator.category'])
            ->get();

        if ($assignments->isEmpty()) {
            return [];
        }

        $categories = [];
        foreach ($assignments as $assignment) {
            foreach ($assignment->results as $result) {
                $catId = $result->indicator->category_id;
                $catName = $result->indicator->category->name;

                if (!isset($categories[$catId])) {
                    $categories[$catId] = [
                        'name' => $catName,
                        'scores' => [],
                    ];
                }
                $categories[$catId]['scores'][] = $result->score;
            }
        }

        $report = [];
        foreach ($categories as $cat) {
            $report[] = [
                'category_name' => $cat['name'],
                'average_score' => round(array_sum($cat['scores']) / count($cat['scores']), 2),
            ];
        }

        return $report;
    }
}
