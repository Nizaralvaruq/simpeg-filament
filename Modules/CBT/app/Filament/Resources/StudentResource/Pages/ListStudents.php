<?php

namespace Modules\CBT\Filament\Resources\StudentResource\Pages;

use Modules\CBT\Filament\Resources\StudentResource;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
