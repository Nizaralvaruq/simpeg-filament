<?php

namespace Modules\Presensi\Filament\Resources\JadwalPiketResource\Pages;

use Modules\Presensi\Filament\Resources\JadwalPiketResource;
use Filament\Resources\Pages\EditRecord;

class EditJadwalPiket extends EditRecord
{
    protected static string $resource = JadwalPiketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
