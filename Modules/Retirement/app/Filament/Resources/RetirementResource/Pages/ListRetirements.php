<?php

namespace Modules\Retirement\Filament\Resources\RetirementResource\Pages;

use Modules\Retirement\Filament\Resources\RetirementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRetirements extends ListRecords
{
    protected static string $resource = RetirementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
