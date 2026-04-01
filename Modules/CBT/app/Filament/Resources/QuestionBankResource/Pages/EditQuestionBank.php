<?php

namespace Modules\CBT\Filament\Resources\QuestionBankResource\Pages;

use Modules\CBT\Filament\Resources\QuestionBankResource;
use Filament\Resources\Pages\EditRecord;

class EditQuestionBank extends EditRecord
{
    protected static string $resource = QuestionBankResource::class;

    public function getMaxContentWidth(): \Filament\Support\Enums\Width|string|null
    {
        return \Filament\Support\Enums\Width::Full;
    }
}
