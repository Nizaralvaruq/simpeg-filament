<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Kepegawaian\Models\DataInduk;

class GenderStatsOverview extends ChartWidget
{
    protected ?string $heading = 'Distribusi Gender Pegawai';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'md';


    protected function getData(): array
    {
        $data = DataInduk::query()
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
}
