<?php

namespace Modules\Resign\Filament\Resources\ResignResource\Pages;

use Modules\Resign\Filament\Resources\ResignResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateResign extends CreateRecord
{
    protected static string $resource = ResignResource::class;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user?->hasRole('staff') && !$user->employee) {
            Notification::make()
                ->title('Profil Belum Lengkap')
                ->body('Anda harus melengkapi Data Induk Pegawai sebelum mengajukan resign.')
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
}
