<?php

namespace Modules\Akademik\Filament\Resources\SetoranNgajiResource\Pages;

use Modules\Akademik\Filament\Resources\SetoranNgajiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSetoranNgaji extends ViewRecord
{
    protected static string $resource = SetoranNgajiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
