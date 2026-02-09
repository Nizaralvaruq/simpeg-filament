<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Kepegawaian\Models\DataInduk;
use Modules\MasterData\Models\Unit;

class GrafikDistribusiPegawai extends ChartWidget
{
    protected ?string $heading = 'Distribusi Pegawai per Unit';
    protected static ?int $sort = 40;
    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        return $user && $user->can('View:GrafikDistribusiPegawai');
    }


    protected function getData(): array
    {
        $units = Unit::withCount(['employees' => function ($query) {
            $query->whereIn('status', ['aktif', 'Aktif']);
        }])->get();

        $labels = $units->pluck('name')->toArray();
        $data = $units->pluck('employees_count')->toArray();

        // Modern Palette (Tailwind-ish)
        $colors = [
            '#3b82f6', // blue-500
            '#10b981', // emerald-500
            '#f59e0b', // amber-500
            '#ef4444', // red-500
            '#8b5cf6', // violet-500
            '#ec4899', // pink-500
            '#06b6d4', // cyan-500
            '#f97316', // orange-500
        ];

        // Accessor to cycle through colors if data > colors
        $chartColors = collect($data)->map(function ($value, $key) use ($colors) {
            return $colors[$key % count($colors)];
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pegawai',
                    'data' => $data,
                    'backgroundColor' => $chartColors,
                    'borderWidth' => 0,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
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
                    'position' => 'right',
                ],
            ],
            'cutout' => '70%', // Thinner doughnut looks more modern
        ];
    }
}
