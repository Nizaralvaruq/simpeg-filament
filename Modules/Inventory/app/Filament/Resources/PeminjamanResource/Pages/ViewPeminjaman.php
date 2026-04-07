<?php

namespace Modules\Inventory\Filament\Resources\PeminjamanResource\Pages;

use Modules\Inventory\Filament\Resources\PeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPeminjaman extends ViewRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
