<?php

namespace Modules\Inventory\Filament\Resources\PermintaanBarangResource\Pages;

use Modules\Inventory\Filament\Resources\PermintaanBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePermintaanBarang extends CreateRecord
{
    protected static string $resource = PermintaanBarangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }
}
