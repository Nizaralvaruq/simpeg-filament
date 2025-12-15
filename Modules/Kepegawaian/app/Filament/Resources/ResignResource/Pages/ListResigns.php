<?php

namespace Modules\Kepegawaian\Filament\Resources\ResignResource\Pages;

use Modules\Kepegawaian\Filament\Resources\ResignResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListResigns extends ListRecords
{
    protected static string $resource = ResignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
