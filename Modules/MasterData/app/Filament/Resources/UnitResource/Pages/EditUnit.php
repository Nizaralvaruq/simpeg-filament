<?php

namespace Modules\MasterData\Filament\Resources\UnitResource\Pages;

use Modules\MasterData\Filament\Resources\UnitResource;
use Filament\Resources\Pages\EditRecord;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    protected string $view = 'masterdata::filament.resources.unit-resource.pages.edit-unit';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}
