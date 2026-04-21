<?php

namespace Modules\Akademik\Filament\Resources\SetoranNgajiResource\Pages;

use Modules\Akademik\Filament\Resources\SetoranNgajiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetoranNgaji extends EditRecord
{
    protected static string $resource = SetoranNgajiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
