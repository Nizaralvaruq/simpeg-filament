<?php

namespace Modules\Inventory\Filament\Resources\KategoriBarangResource\Pages;

use Modules\Inventory\Filament\Resources\KategoriBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriBarang extends EditRecord
{
    protected static string $resource = KategoriBarangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
