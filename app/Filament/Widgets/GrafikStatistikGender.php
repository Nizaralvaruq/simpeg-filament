<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Kepegawaian\Models\DataInduk;

class GrafikStatistikGender extends ChartWidget
{
    protected ?string $heading = 'Distribusi Gender Pegawai';
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        return $user && $user->can('View:GrafikStatistikGender');
    }


    protected function getData(): array
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = DataInduk::query();

        // Filter for local admins: Only show employees in their units
        if (!$user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id')->all();
                $query->whereHas('units', function ($q) use ($unitIds) {
                    $q->whereIn('units.id', $unitIds);
                });
            } else {
                $query->whereRaw('1=0'); // No units assigned
            }
        }

        $data = $query
            ->selectRaw('jenis_kelamin, count(*) as count')
            ->groupBy('jenis_kelamin')
            ->pluck('count', 'jenis_kelamin')
            ->toArray();

        // Ensure keys handle null or empty values gracefully if needed, 
        // though typically they will be 'Laki-laki' and 'Perempuan' if data is clean.
        $male = $data['Laki-laki'] ?? 0;
        $female = $data['Perempuan'] ?? 0;
        // Handle null/others if any
        $others = array_sum($data) - $male - $female;

        $datasets = [
            [
                'label' => 'Gender',
                'data' => [$male, $female, $others],
                'backgroundColor' => [
                    '#3b82f6', // Blue for Male
                    '#ec4899', // Pink for Female
                    '#9ca3af', // Gray for Others/Misisng
                ],
            ],
        ];

        return [
            'datasets' => $datasets,
            'labels' => ['Laki-laki', 'Perempuan', 'Lainnya'],
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
                    'position' => 'bottom', // Legend di bawah agar lebih rapi
                ],
            ],
            'cutout' => '70%', // Lingkaran tipis (Modern look)
        ];
    }
}
