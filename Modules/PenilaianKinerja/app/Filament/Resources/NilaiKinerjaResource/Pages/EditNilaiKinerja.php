<?php

namespace Modules\PenilaianKinerja\Filament\Resources\NilaiKinerjaResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\NilaiKinerjaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditNilaiKinerja extends EditRecord
{
    protected static string $resource = NilaiKinerjaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
