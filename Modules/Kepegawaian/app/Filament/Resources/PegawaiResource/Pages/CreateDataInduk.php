<?php

namespace Modules\Kepegawaian\Filament\Resources\PegawaiResource\Pages;

use Modules\Kepegawaian\Filament\Resources\PegawaiResource;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Filament\Notifications\Notification;

class CreateDataInduk extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();
        $record = $this->record;

        if (!empty($data['email']) && !empty($data['password'])) {
            $user = User::create([
                'name' => $record->nama,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('staff');

            $record->update(['user_id' => $user->id]);

            Notification::make()
                ->title('Akun Login Berhasil Dibuat')
                ->success()
                ->send();
        }
    }
}
