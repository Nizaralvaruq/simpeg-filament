<?php

namespace Modules\PenilaianKinerja\Filament\Resources\SesiPenilaianResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\SesiPenilaianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSesiPenilaian extends ListRecords
{
    protected static string $resource = SesiPenilaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \Modules\PenilaianKinerja\Filament\Widgets\PenilaianStatsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}

