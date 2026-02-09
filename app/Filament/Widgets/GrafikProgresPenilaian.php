<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Illuminate\Support\Facades\Auth;

class GrafikProgresPenilaian extends ChartWidget
{
    protected static ?int $sort = 5;
    protected ?string $heading = 'Progres Sesi Penilaian Aktif';
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '250px';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && $user->can('View:GrafikProgresPenilaian');
    }

    protected function getData(): array
    {
        $activeSession = AppraisalSession::where('is_active', true)->latest()->first();

        if (!$activeSession) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = AppraisalAssignment::where('session_id', $activeSession->id);

        if (!$user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah'])) {
            $unitIds = $user->employee?->units->pluck('id')->toArray() ?? [];
            $query->whereHas('ratee.units', fn($q) => $q->whereIn('units.id', $unitIds));
        }

        $allAssignments = (clone $query)->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $pending = $allAssignments - $completed;

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tugas',
                    'data' => [$completed, $pending],
                    'backgroundColor' => ['#0ea5e9', '#e2e8f0'], // Sky-500 and Gray-200
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Selesai', 'Belum Selesai'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '70%',
        ];
    }
}
