<?php

namespace Modules\Presensi\Filament\Resources\KegiatanResource\Pages;

use Modules\Presensi\Filament\Resources\KegiatanResource;
use Filament\Resources\Pages\EditRecord;

class EditKegiatan extends EditRecord
{
    protected static string $resource = KegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
