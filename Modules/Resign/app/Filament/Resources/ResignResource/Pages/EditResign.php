<?php

namespace Modules\Resign\Filament\Resources\ResignResource\Pages;

use Modules\Resign\Filament\Resources\ResignResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditResign extends EditRecord
{
    protected static string $resource = ResignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Automatically set approved_by if status is changed to approved/rejected
        if (isset($data['status']) && in_array($data['status'], ['disetujui', 'ditolak'])) {
            $data['approved_by'] = Auth::id();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
