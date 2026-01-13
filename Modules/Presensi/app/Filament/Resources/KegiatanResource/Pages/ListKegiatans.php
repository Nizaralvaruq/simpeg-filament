<?php

namespace Modules\Presensi\Filament\Resources\KegiatanResource\Pages;

use Modules\Presensi\Filament\Resources\KegiatanResource;
use Filament\Resources\Pages\ListRecords;

class ListKegiatans extends ListRecords
{
    protected static string $resource = KegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
