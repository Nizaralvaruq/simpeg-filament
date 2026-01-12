<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Presensi\Models\Absensi;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah', 'admin_unit', 'koor_jenjang']);
    }

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ---------------------------------------------------------
        // CARD 1: PEGAWAI TERLAMBAT (Late Employees)
        // ---------------------------------------------------------
        $today = Carbon::today();
        $standardWorkTime = '07:00:00';

        // Base query absensi hari ini + hadir
        $queryAbsensi = Absensi::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->whereNotNull('jam_masuk');

        // Context Logic (Unit vs Global)
        $isGlobalAdmin = $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah']);
        $labelContext = 'Global';

        if (!$isGlobalAdmin && $user->hasAnyRole(['admin_unit', 'koor_jenjang'])) {
            $labelContext = 'Unit';
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                $queryAbsensi->whereHas('user.employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                // If unit admin has no unit, 0 results
                $queryAbsensi->whereRaw('1 = 0');
            }
        }

        $lateCount = (clone $queryAbsensi)->where('jam_masuk', '>', $standardWorkTime)->count();
        $totalPresentToday = $queryAbsensi->count();
        $latePercentage = $totalPresentToday > 0 ? ($lateCount / $totalPresentToday) * 100 : 0;

        $lateColor = 'success';
        if ($latePercentage > 20) $lateColor = 'danger';
        elseif ($latePercentage > 10) $lateColor = 'warning';

        // ---------------------------------------------------------
        // SETUP PENILAIAN (Common for Card 2 & 3)
        // ---------------------------------------------------------
        $activeSession = AppraisalSession::where('is_active', true)->latest()->first();

        $progressStat = Stat::make('Progres Penilaian', 'Tidak ada sesi')->color('gray');
        $scoreStat = Stat::make('Rata-rata Skor', '-')->color('gray');

        if ($activeSession) {
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
            // CARD 2: PROGRES PENILAIAN
            // ---------------------------------------------------------
            $totalAssignments = (clone $queryAssignments)->count();
            $completedAssignments = (clone $queryAssignments)->where('status', 'completed')->count();
            $progress = $totalAssignments > 0 ? ($completedAssignments / $totalAssignments) * 100 : 0;

            $progressStat = Stat::make("Progres Penilaian ({$labelContext})", number_format($progress, 1) . '%')
                ->description("{$completedAssignments} dari {$totalAssignments} tugas selesai")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($progress > 80 ? 'success' : ($progress > 40 ? 'warning' : 'danger'));

            // ---------------------------------------------------------
            // CARD 3: RATA-RATA SKOR (OPTIMIZED)
            // ---------------------------------------------------------
            // Fetch all COMPLETED assignments with results eagerly loaded
            // Only need: ratee_id, relation_type, results.score
            $completedData = (clone $queryAssignments)
                ->where('status', 'completed')
                ->with(['results' => function ($q) {
                    $q->select('assignment_id', 'score'); // optimize select
                }])
                ->get(['id', 'ratee_id', 'relation_type']);

            // Group by Ratee
            $groupedByRatee = $completedData->groupBy('ratee_id');

            $weights = [
                'superior' => $activeSession->superior_weight ?? 50,
                'peer' => $activeSession->peer_weight ?? 30,
                'self' => $activeSession->self_weight ?? 20,
            ];

            $rateeFinalScores = [];

            foreach ($groupedByRatee as $rateeId => $assignments) {
                // Calculate score for this ratee
                $scoresByType = ['superior' => [], 'peer' => [], 'self' => []];

                foreach ($assignments as $assignment) {
                    // Check assignment results average
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

                // Normalize
                if ($totalWeight > 0 && $totalWeight < 100) {
                    $finalScore = ($finalScore / $totalWeight) * 100;
                }

                if ($totalWeight > 0) {
                    $rateeFinalScores[] = $finalScore;
                }
            }

            $globalAvg = 0;
            if (count($rateeFinalScores) > 0) {
                $globalAvg = array_sum($rateeFinalScores) / count($rateeFinalScores);
            }

            $scoreStat = Stat::make("Rata-rata Skor ({$labelContext})", number_format($globalAvg, 2))
                ->description("Hasil dari {$activeSession->name}")
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success');
        }

        return [
            Stat::make("Pegawai Terlambat ({$labelContext})", $lateCount)
                ->description(number_format($latePercentage, 1) . '% dari ' . $totalPresentToday . ' kehadiran')
                ->descriptionIcon('heroicon-m-clock')
                ->color($lateColor)
                ->chart($this->getLateChartData($user, $standardWorkTime)), // Re-use logic

            $progressStat,
            $scoreStat
        ];
    }

    protected function getLateChartData($user, $standardWorkTime): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $query = Absensi::whereDate('tanggal', $date)
                ->where('status', 'hadir')
                ->whereNotNull('jam_masuk')
                ->where('jam_masuk', '>', $standardWorkTime);

            if (
                !$user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah'])
                && $user->hasAnyRole(['admin_unit', 'koor_jenjang'])
            ) {
                if ($user->employee && $user->employee->units->isNotEmpty()) {
                    $unitIds = $user->employee->units->pluck('id');
                    $query->whereHas('user.employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
                }
            }
            $data[] = $query->count();
        }
        return $data;
    }
}
