<?php

namespace Modules\MasterData\Filament\Resources\UnitTypeResource\Pages;

use Modules\MasterData\Filament\Resources\UnitTypeResource;
use Filament\Resources\Pages\EditRecord;

class EditUnitType extends EditRecord
{
    protected static string $resource = UnitTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
