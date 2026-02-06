<?php

namespace Modules\PenilaianKinerja\Filament\Resources\NilaiKinerjaResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\NilaiKinerjaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNilaiKinerja extends ListRecords
{
    protected static string $resource = NilaiKinerjaResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \Modules\PenilaianKinerja\Filament\Widgets\UnitEmployeeListWidget::class,
        ];
    }
}
