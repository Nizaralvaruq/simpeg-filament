<?php

namespace Modules\Kepegawaian\Filament\Resources\PegawaiResource\Pages;

use Modules\Kepegawaian\Filament\Resources\PegawaiResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListDataInduks extends ListRecords
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
