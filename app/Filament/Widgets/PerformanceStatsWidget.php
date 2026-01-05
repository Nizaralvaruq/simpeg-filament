<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\PenilaianKinerja\Models\PerformanceScore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PerformanceStatsWidget extends BaseWidget
{
    protected static ?int $sort = -5;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $currentMonth = now()->format('Y-m');
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Base query
        $query = PerformanceScore::where('periode', $currentMonth);

        // Filter jika Koordinator Jenjang (bukan Super Admin / Kepala Sekolah)
        // Jika user punya multiple roles, prioritas role tertinggi
        $isGlobalAdmin = $user && $user->hasRole(['super_admin', 'kepala_sekolah', 'yayasan']);
        $isKoor = $user && $user->hasRole('koor_jenjang');

        if ($isKoor && !$isGlobalAdmin) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                $query->whereHas('employee.units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }
        }

        $scores = $query->get();
        $totalThisMonth = $scores->count();

        if ($scores->isEmpty()) {
            $avgScore = 0;
            $gradeA = 0;
            $gradeB = 0;
        } else {
            $totalScore = 0;
            $gradeA = 0;
            $gradeB = 0;

            foreach ($scores as $score) {
                // Kalkulasi manual untuk memastikan akurasi
                $avg = ($score->kualitas_hasil + $score->ketelitian + $score->kuantitas_hasil +
                    $score->ketepatan_waktu + $score->kehadiran + $score->kepatuhan_aturan +
                    $score->etika_kerja + $score->tanggung_jawab + $score->komunikasi +
                    $score->kerjasama_tim) / 10.0;

                $totalScore += $avg;

                if ($avg >= 4.51) {
                    $gradeA++;
                } elseif ($avg >= 3.76) {
                    $gradeB++;
                }
            }

            $avgScore = $totalScore / $scores->count();
        }

        $labelContext = 'Global';
        if ($isKoor && !$isGlobalAdmin) {
            $labelContext = 'Unit';
        }

        return [
            Stat::make("Total Penilaian ({$labelContext})", $totalThisMonth)
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make("Rata-rata IPK ({$labelContext})", number_format($avgScore, 2))
                ->description('Skor kinerja rata-rata')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),

            Stat::make('Grade A', $gradeA)
                ->description('Pegawai performa istimewa')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            Stat::make('Grade B', $gradeB)
                ->description('Pegawai performa sangat baik')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('info'),
        ];
    }
}
