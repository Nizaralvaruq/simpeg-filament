<?php

namespace Modules\Resign\Filament\Resources\ResignResource\Pages;

use Modules\Resign\Filament\Resources\ResignResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListResigns extends ListRecords
{
    protected static string $resource = ResignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pengajuan Resign'),
        ];
    }
}
