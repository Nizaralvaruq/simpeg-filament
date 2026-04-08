<?php

namespace Modules\Inventory\Filament\Resources\PeminjamanResource\Pages;

use Modules\Inventory\Filament\Resources\PeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePeminjaman extends CreateRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'diajukan';
        return $data;
    }
}
