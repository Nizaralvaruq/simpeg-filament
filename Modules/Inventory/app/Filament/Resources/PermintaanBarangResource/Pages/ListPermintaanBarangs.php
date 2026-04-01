<?php

namespace Modules\Inventory\Filament\Resources\PermintaanBarangResource\Pages;

use Modules\Inventory\Filament\Resources\PermintaanBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermintaanBarangs extends ListRecords
{
    protected static string $resource = PermintaanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
