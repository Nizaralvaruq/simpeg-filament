<?php

namespace Modules\Retirement\Filament\Resources\RetirementResource\Pages;

use Modules\Retirement\Filament\Resources\RetirementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRetirement extends CreateRecord
{
    protected static string $resource = RetirementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
