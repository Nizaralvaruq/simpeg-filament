<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatistikPenilaianKinerja extends BaseWidget
{
    protected static ?int $sort = 21;
    protected int | string | array $columnSpan = 1;
    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && $user->can('View:StatistikPenilaianKinerja');
    }

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $activeSession = AppraisalSession::where('is_active', true)->latest()->first();

        if (!$activeSession) {
            return [
                Stat::make('Progres Penilaian', 'Tidak ada sesi')->color('gray'),
                Stat::make('Rata-rata Skor', '-')->color('gray'),
            ];
        }

        $isGlobalAdmin = $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah']);
        $labelContext = $isGlobalAdmin ? 'Global' : 'Unit';

        // Query Assignments
        $queryAssignments = AppraisalAssignment::where('session_id', $activeSession->id);

        if (!$isGlobalAdmin && $user->hasAnyRole(['admin_unit', 'koor_jenjang'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                $queryAssignments->whereHas('ratee.units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                $queryAssignments->whereRaw('1 = 0');
            }
        }

        // ---------------------------------------------------------
        // CARD 1: PROGRES PENILAIAN
        // ---------------------------------------------------------
        $totalAssignments = (clone $queryAssignments)->count();
        $completedAssignments = (clone $queryAssignments)->where('status', 'completed')->count();
        $progress = $totalAssignments > 0 ? ($completedAssignments / $totalAssignments) * 100 : 0;

        $progressStat = Stat::make("Progres Penilaian ({$labelContext})", number_format($progress, 1) . '%')
            ->description("{$completedAssignments} dari {$totalAssignments} tugas selesai")
            ->descriptionIcon('heroicon-m-check-circle')
            ->color($progress > 80 ? 'success' : ($progress > 40 ? 'warning' : 'danger'));

        // ---------------------------------------------------------
        // CARD 2: RATA-RATA SKOR
        // ---------------------------------------------------------
        $completedData = (clone $queryAssignments)
            ->where('status', 'completed')
            ->with(['results' => function ($q) {
                $q->select('assignment_id', 'score');
            }])
            ->get(['id', 'ratee_id', 'relation_type']);

        $groupedByRatee = $completedData->groupBy('ratee_id');

        $weights = [
            'superior' => $activeSession->superior_weight ?? 50,
            'peer' => $activeSession->peer_weight ?? 30,
            'self' => $activeSession->self_weight ?? 20,
        ];

        $rateeFinalScores = [];

        foreach ($groupedByRatee as $rateeId => $assignments) {
            $scoresByType = ['superior' => [], 'peer' => [], 'self' => []];

            foreach ($assignments as $assignment) {
                if ($assignment->results->isNotEmpty()) {
                    $avgResult = $assignment->results->avg('score');
                    if ($assignment->relation_type && isset($scoresByType[$assignment->relation_type])) {
                        $scoresByType[$assignment->relation_type][] = $avgResult;
                    }
                }
            }

            $finalScore = 0;
            $totalWeight = 0;

            foreach ($scoresByType as $type => $scores) {
                if (!empty($scores)) {
                    $avgType = array_sum($scores) / count($scores);
                    $finalScore += $avgType * ($weights[$type] / 100);
                    $totalWeight += $weights[$type];
                }
            }

            if ($totalWeight > 0 && $totalWeight < 100) {
                $finalScore = ($finalScore / $totalWeight) * 100;
            }

            if ($totalWeight > 0) {
                $rateeFinalScores[] = $finalScore;
            }
        }

        $globalAvg = count($rateeFinalScores) > 0 ? array_sum($rateeFinalScores) / count($rateeFinalScores) : 0;

        $scoreStat = Stat::make("Rata-rata Skor ({$labelContext})", number_format($globalAvg, 2))
            ->description("Hasil dari {$activeSession->name}")
            ->descriptionIcon('heroicon-m-chart-bar')
            ->color('success');

        return [$progressStat, $scoreStat];
    }
}
