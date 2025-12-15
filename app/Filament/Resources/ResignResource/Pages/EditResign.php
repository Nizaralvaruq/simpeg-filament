<?php

namespace App\Filament\Resources\ResignResource\Pages;

use App\Filament\Resources\ResignResource;
use Filament\Resources\Pages\EditRecord;

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
            $data['approved_by'] = auth()->id();
        }

        return $data;
    }
}
