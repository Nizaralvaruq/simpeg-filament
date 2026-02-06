<?php

namespace Modules\PenilaianKinerja\Filament\Resources\RubrikPenilaianResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\RubrikPenilaianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRubrikPenilaian extends ListRecords
{
    protected static string $resource = RubrikPenilaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Rubrik Penilaian'),
        ];
    }
}
