<?php

namespace Modules\Akademik\Filament\Resources\SiswaResource\Pages;

use Modules\Akademik\Filament\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
