<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class GrafikTrenKehadiran extends ChartWidget
{
    protected static ?int $sort = 4;
    protected ?string $heading = 'Tren Keterlambatan (7 Hari Terakhir)';
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '250px';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && $user->can('View:GrafikTrenKehadiran');
    }

    protected function getData(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = [];
        $labels = [];

        // Get last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d M');

            $isGlobalAdmin = $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah']);

            $query = Absensi::query()
                ->whereDate('tanggal', $date)
                ->where('late_minutes', '>', 0)
                ->when(!$isGlobalAdmin, function ($q) use ($user) {
                    $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                    if (!empty($unitIds)) {
                        $q->whereHas('user.employee.units', fn($sq) => $sq->whereIn('units.id', $unitIds));
                    } else {
                        $q->whereRaw('1=0');
                    }
                });

            $data[] = $query->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pegawai Terlambat',
                    'data' => $data,
                    'borderColor' => '#0ea5e9', // Sky Blue
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#0ea5e9',
                    'pointBorderColor' => '#fff',
                    'pointRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
