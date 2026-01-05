<?php

namespace Modules\PenilaianKinerja\Filament\Resources\PerformanceScoreResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\PerformanceScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerformanceScore extends EditRecord
{
    protected static string $resource = PerformanceScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
