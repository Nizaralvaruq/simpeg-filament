<?php

namespace Modules\Retirement\Filament\Resources\RetirementResource\Pages;

use Modules\Retirement\Filament\Resources\RetirementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRetirement extends EditRecord
{
    protected static string $resource = RetirementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
