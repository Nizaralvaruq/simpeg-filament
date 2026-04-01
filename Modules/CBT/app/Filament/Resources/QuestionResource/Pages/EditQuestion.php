<?php

namespace Modules\CBT\Filament\Resources\QuestionResource\Pages;

use Modules\CBT\Filament\Resources\QuestionResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditQuestion extends EditRecord
{
    protected static string $resource = QuestionResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }
}
