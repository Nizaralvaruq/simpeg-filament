<?php

namespace Modules\Kepegawaian\Filament\Resources\LeaveRequestResource\Pages;

use Modules\Kepegawaian\Filament\Resources\LeaveRequestResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditLeaveRequest extends EditRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // set approved_by if status is changed to approved/rejected
        if (isset($data['status']) && in_array($data['status'], ['approved', 'rejected'])) {
            $data['approved_by'] = auth()->id();
        }

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
