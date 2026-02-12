<?php

namespace Modules\MasterData\Filament\Resources\UnitResource\Pages;

use Modules\MasterData\Filament\Resources\UnitResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Unit/Jenjang'),
        ];
    }
}
