<?php

namespace Modules\MasterData\Filament\Resources\UnitTypeResource\Pages;

use Modules\MasterData\Filament\Resources\UnitTypeResource;
use Filament\Resources\Pages\ListRecords;

class ListUnitTypes extends ListRecords
{
    protected static string $resource = UnitTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
