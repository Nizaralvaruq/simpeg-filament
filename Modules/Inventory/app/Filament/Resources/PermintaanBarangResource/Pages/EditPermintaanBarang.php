<?php

namespace Modules\Inventory\Filament\Resources\PermintaanBarangResource\Pages;

use Modules\Inventory\Filament\Resources\PermintaanBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanBarang extends EditRecord
{
    protected static string $resource = PermintaanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
