<?php

namespace App\Filament\Resources\ResignResource\Pages;

use App\Filament\Resources\ResignResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateResign extends CreateRecord
{
    protected static string $resource = ResignResource::class;

    public function mount(): void
    {
        if (! auth()->user()->employee) {
            Notification::make()
                ->title('Profil Belum Lengkap')
                ->body('Anda harus melengkapi Data Induk Pegawai sebelum mengajukan resign.')
                ->danger()
                ->send();

            $this->redirect($this->previousUrl ?? '/admin');
            return;
        }

        parent::mount();
    }
}
