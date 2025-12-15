<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    public function mount(): void
    {
        // Check if user has employee data
        $user = auth()->user();
        if ($user->hasRole('staff') && !$user->employee) {
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
}
