<?php

namespace Modules\CBT\Filament\Resources\QuestionBankResource\Pages;

use Modules\CBT\Filament\Resources\QuestionBankResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestionBanks extends ListRecords
{
    protected static string $resource = QuestionBankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
