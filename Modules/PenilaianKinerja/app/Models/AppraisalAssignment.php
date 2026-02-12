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

    protected static $sessionCache = [];

    public static function getAggregatedReport($sessionId, $rateeId)
    {
        $assignments = self::where('session_id', $sessionId)
            ->where('ratee_id', $rateeId)
            ->where('status', 'completed')
            ->with(['results'])
            ->get();

        if ($assignments->isEmpty()) {
            return null;
        }

        if (!isset(self::$sessionCache[$sessionId])) {
            self::$sessionCache[$sessionId] = AppraisalSession::find($sessionId);
        }

        $session = self::$sessionCache[$sessionId];
        $weights = [
            'superior' => $session->superior_weight ?? 50,
            'peer' => $session->peer_weight ?? 30,
            'self' => $session->self_weight ?? 20,
            'attendance' => $session->attendance_weight ?? 0,
            'activity' => $session->activity_weight ?? 0,
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

        // Calculate Attendance Score
        if (!empty($weights['attendance']) && $weights['attendance'] > 0) {
            $ratee = DataInduk::find($rateeId);
            if ($ratee && $ratee->user_id) {
                $attendanceData = \Modules\PenilaianKinerja\Services\AutoScoreService::getAttendanceScore(
                    $ratee->user_id,
                    $session->start_date,
                    $session->end_date
                );
                $finalScore += $attendanceData['score'] * ($weights['attendance'] / 100);
                $totalAvailableWeight += $weights['attendance'];
            }
        }

        // Calculate Activity Score
        if (!empty($weights['activity']) && $weights['activity'] > 0) {
            $ratee = DataInduk::find($rateeId);
            if ($ratee && $ratee->user_id) {
                $activityData = \Modules\PenilaianKinerja\Services\AutoScoreService::getActivityScore(
                    $ratee->user_id,
                    $session->start_date,
                    $session->end_date
                );
                $finalScore += $activityData['score'] * ($weights['activity'] / 100);
                $totalAvailableWeight += $weights['activity'];
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
            $avg = round(array_sum($cat['scores']) / count($cat['scores']), 2);
            $report[] = [
                'category_name' => $cat['name'],
                'average_score' => $avg,
                'grade' => self::getGrade($avg),
            ];
        }

        // Add Virtual Categories for Auto Scores
        $session = AppraisalSession::find($sessionId);
        if ($session) {
            $ratee = DataInduk::find($rateeId);
            if ($ratee && $ratee->user_id) {
                // Kehadiran
                if ($session->attendance_weight > 0) {
                    $att = \Modules\PenilaianKinerja\Services\AutoScoreService::getAttendanceScore($ratee->user_id, $session->start_date, $session->end_date);
                    $report[] = [
                        'category_name' => 'Kehadiran Harian (Otomatis)',
                        'average_score' => $att['score'],
                        'grade' => self::getGrade($att['score']),
                    ];
                }

                // Kegiatan
                if ($session->activity_weight > 0) {
                    $act = \Modules\PenilaianKinerja\Services\AutoScoreService::getActivityScore($ratee->user_id, $session->start_date, $session->end_date);
                    $report[] = [
                        'category_name' => 'Partisipasi Kegiatan (Otomatis)',
                        'average_score' => $act['score'],
                        'grade' => self::getGrade($act['score']),
                    ];
                }
            }
        }

        return $report;
    }

    public static function getGrade($score)
    {
        if ($score >= 4.5) return 'A+ (Istimewa)';
        if ($score >= 4.0) return 'A (Sangat Baik)';
        if ($score >= 3.5) return 'B+ (Baik Sekali)';
        if ($score >= 3.0) return 'B (Baik)';
        if ($score >= 2.5) return 'C (Cukup)';
        if ($score >= 2.0) return 'D (Kurang)';
        return 'E (Sangat Kurang)';
    }
}
