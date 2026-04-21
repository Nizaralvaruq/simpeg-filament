<?php

namespace Modules\Akademik\Filament\Resources\SiswaResource\Pages;

use Modules\Akademik\Filament\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSiswa extends ViewRecord
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
