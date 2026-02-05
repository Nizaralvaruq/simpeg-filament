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

        if (!empty($data['email']) && !empty($data['password'])) {
            $user = User::create([
                'name' => $record->nama,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // Assign role
            if (!empty($data['roles'])) {
                $user->assignRole($data['roles']);
            } else {
                // Fallback: Assign role based on who is creating the account
                /** @var \App\Models\User $creator */
                $creator = Auth::user();
                if ($creator && $creator->hasRole('admin_unit')) {
                    $user->assignRole('admin_unit');
                } else {
                    $user->assignRole('staff');
                }
            }

            $record->update(['user_id' => $user->id]);

            Notification::make()
                ->title('Akun Login Berhasil Dibuat')
                ->success()
                ->send();
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
