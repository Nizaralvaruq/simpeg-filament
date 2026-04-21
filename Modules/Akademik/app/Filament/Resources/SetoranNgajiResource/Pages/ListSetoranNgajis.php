<?php

namespace Modules\Akademik\Filament\Resources\SetoranNgajiResource\Pages;

use Modules\Akademik\Filament\Resources\SetoranNgajiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSetoranNgajis extends ListRecords
{
    protected static string $resource = SetoranNgajiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Input Setoran Baru'),
        ];
    }
}
