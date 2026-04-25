<?php

namespace Modules\Akademik\Filament\Resources\AbsensiKegiatanSiswaResource\Pages;

use Modules\Akademik\Filament\Resources\AbsensiKegiatanSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAbsensiKegiatanSiswas extends ManageRecords
{
    protected static string $resource = AbsensiKegiatanSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
