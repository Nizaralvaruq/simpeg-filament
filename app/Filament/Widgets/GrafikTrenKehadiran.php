<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class GrafikTrenKehadiran extends ChartWidget
{
    protected static ?int $sort = 3;
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
        $isGlobalAdmin = $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah']);

        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $query = Absensi::query()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('late_minutes', '>', 0)
            ->when(!$isGlobalAdmin, function ($q) use ($user) {
                $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                if (!empty($unitIds)) {
                    $q->whereHas('user.employee.units', fn($sq) => $sq->whereIn('units.id', $unitIds));
                } else {
                    $q->whereRaw('1=0');
                }
            })
            ->selectRaw('DATE(tanggal) as date, COUNT(*) as count')
            ->groupByRaw('DATE(tanggal)');

        $countsByDate = $query->get()->keyBy('date')->map(fn($row) => $row->count);

        $data = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $data[] = $countsByDate->get($dateStr, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pegawai Terlambat',
                    'data' => $data,
                    'borderColor' => '#0ea5e9',
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
