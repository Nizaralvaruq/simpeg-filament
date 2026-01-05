<?php

namespace Modules\PenilaianKinerja\Filament\Resources\PerformanceScoreResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\PerformanceScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerformanceScores extends ListRecords
{
    protected static string $resource = PerformanceScoreResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \Modules\PenilaianKinerja\Filament\Widgets\UnitEmployeeListWidget::class,
            \App\Filament\Widgets\PerformanceStatsWidget::class,
        ];
    }
}
