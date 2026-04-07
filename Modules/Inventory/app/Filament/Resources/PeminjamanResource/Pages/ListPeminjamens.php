<?php

namespace Modules\Inventory\Filament\Resources\PeminjamanResource\Pages;

use Modules\Inventory\Filament\Resources\PeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeminjamens extends ListRecords
{
    protected static string $resource = PeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
