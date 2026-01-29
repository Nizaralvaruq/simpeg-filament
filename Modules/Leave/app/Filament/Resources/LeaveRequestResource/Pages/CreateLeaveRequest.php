<?php

namespace Modules\Leave\Filament\Resources\LeaveRequestResource\Pages;

use Modules\Leave\Filament\Resources\LeaveRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    public function mount(): void
    {
        // Check if user has employee data
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user?->hasRole('staff') && !$user->employee) {
            Notification::make()
                ->title('Profil Belum Lengkap')
                ->body('Anda belum memiliki Data Induk Pegawai. Silakan hubungi Admin.')
                ->danger()
                ->send();

            $this->redirect('/');
            return;
        }

        parent::mount();
    }
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Buat Permohonan Izin';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Kirim Permohonan');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Permohonan berhasil dikirim';
    }
}
