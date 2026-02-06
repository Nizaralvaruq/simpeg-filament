<?php

namespace Modules\PenilaianKinerja\Filament\Resources\SesiPenilaianResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\SesiPenilaianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSesiPenilaian extends ListRecords
{
    protected static string $resource = SesiPenilaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Sesi Penilaian'),
        ];
    }
}
