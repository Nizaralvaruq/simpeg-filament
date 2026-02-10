<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RingkasanOperasionalUnit extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected ?string $pollingInterval = '60s';

    protected function getColumns(): int
    {
        return 3;
    }

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Visible if user has permission for either attendance or performance stats
        return $user && (
            $user->can('View:StatistikPegawaiTerlambat') ||
            $user->can('View:StatistikPenilaianKinerja')
        );
    }

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isGlobalAdmin = $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah']);
        $labelContext = $isGlobalAdmin ? 'Global' : 'Unit';

        return [
            $this->getLateStats($user, $isGlobalAdmin, $labelContext),
            ...$this->getPerformanceStats($user, $isGlobalAdmin, $labelContext),
        ];
    }

    protected function getLateStats($user, $isGlobalAdmin, $labelContext): Stat
    {
        $today = Carbon::today();
        $settings = \Modules\MasterData\Models\Setting::get();
        $standardWorkTime = $settings->office_start_time;
        $tolerance = (int) ($settings->late_tolerance ?? 0);

        $effectiveLateTime = Carbon::parse($standardWorkTime)->addMinutes($tolerance)->format('H:i:s');

        $queryAbsensi = Absensi::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->whereNotNull('jam_masuk');

        $isWorkingDay = Absensi::isWorkingDay($today);
        $lateCount = 0;
        $totalPresentToday = 0;
        $latePercentage = 0;

        if ($isWorkingDay) {
            $queryAbsensi->when(!$isGlobalAdmin, function ($q) use ($user) {
                $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                if (!empty($unitIds)) {
                    $q->whereHas('user.employee.units', fn($sq) => $sq->whereIn('units.id', $unitIds));
                } else {
                    $q->whereRaw('1=0');
                }
            });

            $lateCount = (clone $queryAbsensi)->where('jam_masuk', '>', $effectiveLateTime)->count();
            $totalPresentToday = $queryAbsensi->count();
            $latePercentage = $totalPresentToday > 0 ? ($lateCount / $totalPresentToday) * 100 : 0;
        }

        $lateColor = 'success';
        if ($isWorkingDay) {
            if ($latePercentage > 20) $lateColor = 'danger';
            elseif ($latePercentage > 10) $lateColor = 'warning';
        } else {
            $lateColor = 'gray';
        }

        return Stat::make("Pegawai Terlambat ({$labelContext})", $isWorkingDay ? $lateCount : 'Libur')
            ->description($isWorkingDay
                ? number_format($latePercentage, 1) . '% dari ' . $totalPresentToday . ' kehadiran'
                : 'Hari ini bukan hari kerja')
            ->descriptionIcon('heroicon-m-clock')
            ->color($lateColor)
            ->chart($this->getLateChartData($user, $effectiveLateTime));
    }

    protected function getPerformanceStats($user, $isGlobalAdmin, $labelContext): array
    {
        $activeSession = AppraisalSession::where('is_active', true)->latest()->first();

        if (!$activeSession || !$activeSession->isActiveAndOpen()) {
            return [
                Stat::make('Progres Penilaian', 'Tidak ada sesi')
                    ->color('gray')
                    ->chart([0, 0, 0, 0, 0, 0, 0]),
                Stat::make('Rata-rata Skor', '-')
                    ->color('gray')
                    ->chart([0, 0, 0, 0, 0, 0, 0]),
            ];
        }

        $queryAssignments = AppraisalAssignment::where('session_id', $activeSession->id);

        $queryAssignments->when(!$isGlobalAdmin, function ($q) use ($user) {
            $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
            if (!empty($unitIds)) {
                $q->whereHas('ratee.units', fn($sq) => $sq->whereIn('units.id', $unitIds));
            } else {
                $q->whereRaw('1=0');
            }
        });

        // Progres
        $totalAssignments = (clone $queryAssignments)->count();
        $completedAssignments = (clone $queryAssignments)->where('status', 'completed')->count();
        $progress = $totalAssignments > 0 ? ($completedAssignments / $totalAssignments) * 100 : 0;

        $progressStat = Stat::make("Progres Penilaian ({$labelContext})", number_format($progress, 1) . '%')
            ->description("{$completedAssignments} dari {$totalAssignments} tugas selesai")
            ->descriptionIcon('heroicon-m-check-circle')
            ->color($progress > 80 ? 'success' : ($progress > 40 ? 'warning' : 'danger'))
            ->chart([7, 10, 5, 12, 18, 14, 20]);

        // Skor
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
            ->color('success')
            ->chart([80, 82, 85, 83, 88, 87, 90]);

        return [$progressStat, $scoreStat];
    }

    protected function getLateChartData($user, $effectiveLateTime): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $query = Absensi::whereDate('tanggal', $date)
                ->where('status', 'hadir')
                ->whereNotNull('jam_masuk')
                ->where('jam_masuk', '>', $effectiveLateTime);


            if (Absensi::isWorkingDay($date)) {
                $query->when(!$user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah']), function ($q) use ($user) {
                    $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                    if (!empty($unitIds)) {
                        $q->whereHas('user.employee.units', fn($sq) => $sq->whereIn('units.id', $unitIds));
                    } else {
                        $q->whereRaw('1=0');
                    }
                });
                $data[] = $query->count();
            } else {
                $data[] = 0;
            }
        }
        return $data;
    }
}
