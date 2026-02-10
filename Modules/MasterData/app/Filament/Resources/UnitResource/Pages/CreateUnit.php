<?php

namespace Modules\MasterData\Filament\Resources\UnitResource\Pages;

use Modules\MasterData\Filament\Resources\UnitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    protected string $view = 'masterdata::filament.resources.unit-resource.pages.create-unit';
}
