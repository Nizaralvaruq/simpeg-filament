<?php

namespace Modules\PenilaianKinerja\Filament\Resources\PerformanceScoreResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\PerformanceScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPerformanceScore extends ViewRecord
{
    protected static string $resource = PerformanceScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
