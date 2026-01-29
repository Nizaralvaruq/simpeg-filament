<?php

namespace Modules\Leave\Filament\Resources\LeaveRequestResource\Pages;

use Modules\Leave\Filament\Resources\LeaveRequestResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;

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
            $data['approved_by'] = Auth::id();
        }

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Ubah Data Permohonan';
    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Simpan Perubahan');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Permohonan berhasil diperbarui';
    }
}
