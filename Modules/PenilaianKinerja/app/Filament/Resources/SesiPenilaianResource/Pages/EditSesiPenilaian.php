<?php

namespace Modules\PenilaianKinerja\Filament\Resources\SesiPenilaianResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\SesiPenilaianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSesiPenilaian extends EditRecord
{
    protected static string $resource = SesiPenilaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
