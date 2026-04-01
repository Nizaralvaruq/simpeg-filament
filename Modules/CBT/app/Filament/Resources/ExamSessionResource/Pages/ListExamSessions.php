<?php

namespace Modules\CBT\Filament\Resources\ExamSessionResource\Pages;

use Modules\CBT\Filament\Resources\ExamSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamSessions extends ListRecords
{
    protected static string $resource = ExamSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
