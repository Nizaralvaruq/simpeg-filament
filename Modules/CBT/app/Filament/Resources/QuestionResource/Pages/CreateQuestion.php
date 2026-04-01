<?php

namespace Modules\CBT\Filament\Resources\QuestionResource\Pages;

use Modules\CBT\Filament\Resources\QuestionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }
}
