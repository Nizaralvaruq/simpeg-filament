<?php

namespace Modules\MasterData\Filament\Resources\GolonganResource\Pages;

use Modules\MasterData\Filament\Resources\GolonganResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListGolongans extends ListRecords
{
    protected static string $resource = GolonganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Golongan'),
        ];
    }
}
