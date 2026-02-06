<?php

namespace Modules\PenilaianKinerja\Filament\Resources\RubrikPenilaianResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\RubrikPenilaianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRubrikPenilaian extends EditRecord
{
    protected static string $resource = RubrikPenilaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
