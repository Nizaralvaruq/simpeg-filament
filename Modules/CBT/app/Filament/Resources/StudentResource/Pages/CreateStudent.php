<?php

namespace Modules\CBT\Filament\Resources\StudentResource\Pages;

use Modules\CBT\Filament\Resources\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Enums\Width;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Create the User record
        $user = User::create([
            'name' => $data['user_name'],
            'email' => $data['user_email'],
            'password' => Hash::make($data['user_password']),
        ]);

        // 2. Assign 'siswa' role
        $user->assignRole('siswa');

        // 3. Set user_id for the Student record
        $data['user_id'] = $user->id;

        // Cleanup virtual fields
        unset($data['user_name']);
        unset($data['user_email']);
        unset($data['user_password']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
