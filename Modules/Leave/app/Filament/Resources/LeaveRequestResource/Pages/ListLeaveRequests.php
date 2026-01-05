<?php

namespace Modules\Leave\Filament\Resources\LeaveRequestResource\Pages;

use Modules\Leave\Filament\Resources\LeaveRequestResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListLeaveRequests extends ListRecords
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
