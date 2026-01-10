<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Illuminate\Support\Facades\Auth;

class PerformanceDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Performa Pegawai';
    protected static ?int $sort = 4;
    protected ?string $pollingInterval = null;

    // Opsi ukuran: 'full' (penuh), 'md' (setengah), atau angka 1-12
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cari Sesi Aktif terbaru
        $activeSession = AppraisalSession::where('is_active', true)
            ->latest()
            ->first();

        if (!$activeSession) {
            return [
                'datasets' => [
                    [
                        'label' => 'Jumlah Pegawai',
                        'data' => [0, 0, 0, 0, 0],
                        'backgroundColor' => [
                            'rgba(34, 197, 94, 0.8)',   // Green for A
                            'rgba(59, 130, 246, 0.8)',  // Blue for B
                            'rgba(234, 179, 8, 0.8)',   // Yellow for C
                            'rgba(249, 115, 22, 0.8)',  // Orange for D
                            'rgba(239, 68, 68, 0.8)',   // Red for E
                        ],
                    ],
                ],
                'labels' => ['A (Istimewa)', 'B (Sangat Baik)', 'C (Baik)', 'D (Cukup)', 'E (Kurang)'],
            ];
        }

        // Base query untuk penugasan di sesi ini
        $query = AppraisalAssignment::where('session_id', $activeSession->id);

        // Filter unit jika bukan global admin
        $isGlobalAdmin = $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah']);

        if (!$isGlobalAdmin && $user->hasAnyRole(['admin_unit', 'koor_jenjang'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                $query->whereHas('ratee.units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                return [
                    'datasets' => [['label' => 'Jumlah Pegawai', 'data' => [0, 0, 0, 0, 0]]],
                    'labels' => ['A', 'B', 'C', 'D', 'E'],
                ];
            }
        }

        // Hitung distribusi grade
        $rateeIds = (clone $query)->distinct()->pluck('ratee_id');
        $gradeCount = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];

        foreach ($rateeIds as $rateeId) {
            $finalScore = AppraisalAssignment::getAggregatedReport($activeSession->id, $rateeId);
            if ($finalScore) {
                if ($finalScore >= 4.5) $gradeCount['A']++;
                elseif ($finalScore >= 3.75) $gradeCount['B']++;
                elseif ($finalScore >= 3.0) $gradeCount['C']++;
                elseif ($finalScore >= 2.0) $gradeCount['D']++;
                else $gradeCount['E']++;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pegawai',
                    'data' => array_values($gradeCount),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // Green for A
                        'rgba(59, 130, 246, 0.8)',  // Blue for B
                        'rgba(234, 179, 8, 0.8)',   // Yellow for C
                        'rgba(249, 115, 22, 0.8)',  // Orange for D
                        'rgba(239, 68, 68, 0.8)',   // Red for E
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(234, 179, 8)',
                        'rgb(249, 115, 22)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['A (Istimewa)', 'B (Sangat Baik)', 'C (Baik)', 'D (Cukup)', 'E (Kurang)'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
