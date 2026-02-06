<?php

namespace Modules\PenilaianKinerja\Filament\Resources\NilaiKinerjaResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\NilaiKinerjaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNilaiKinerja extends ViewRecord
{
    protected static string $resource = NilaiKinerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
