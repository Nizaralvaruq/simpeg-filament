<?php

namespace Modules\MasterData\Filament\Resources\GolonganResource\Pages;

use Modules\MasterData\Filament\Resources\GolonganResource;
use Filament\Resources\Pages\EditRecord;

class EditGolongan extends EditRecord
{
    protected static string $resource = GolonganResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
