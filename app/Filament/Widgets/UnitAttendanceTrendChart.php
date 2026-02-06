<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Colors\Color;

class UnitAttendanceTrendChart extends ChartWidget
{
    protected static ?int $sort = 4;
    protected ?string $heading = 'Tren Keterlambatan (7 Hari Terakhir)';
    protected int | string | array $columnSpan = 1;
    protected ?string $maxHeight = '250px';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['kepala_sekolah', 'admin_unit', 'super_admin']);
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

            $query = Absensi::query()
                ->whereDate('tanggal', $date)
                ->where('late_minutes', '>', 0);

            if (!$user->hasRole('super_admin')) {
                $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                $query->whereHas('user.employee.units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            }

            $data[] = $query->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pegawai Terlambat',
                    'data' => $data,
                    'borderColor' => '#f59e0b', // Warning color
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
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
