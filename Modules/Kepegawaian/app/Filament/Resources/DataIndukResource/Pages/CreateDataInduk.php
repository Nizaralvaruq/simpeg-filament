<?php

namespace Modules\Kepegawaian\Filament\Resources\DataIndukResource\Pages;

use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Filament\Notifications\Notification;
use Modules\Kepegawaian\Filament\Resources\DataIndukResource;

class CreateDataInduk extends CreateRecord
{
    protected static string $resource = DataIndukResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();
        $record = $this->record;

        // Use NPA (NIP) as email if not provided, or if email is provided use it.
        // Default password is 'password' if not provided.
        $email = !empty($data['email']) ? $data['email'] : ($record->nip ? $record->nip . '@ihya.com' : null);
        $password = !empty($data['password']) ? $data['password'] : 'password';

        if ($email) {
            // Check if user already exists
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $record->nama,
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);

                // Assign role
                if (!empty($data['roles'])) {
                    $user->assignRole($data['roles']);
                } else {
                    // Fallback to staff
                    $user->assignRole('staff');
                }

                Notification::make()
                    ->title('Akun Login Berhasil Dibuat')
                    ->body("Email: {$email} | Password: " . (!empty($data['password']) ? 'Sesuai input' : 'password'))
                    ->success()
                    ->send();
            }

            $record->update(['user_id' => $user->id]);
        }
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
}
