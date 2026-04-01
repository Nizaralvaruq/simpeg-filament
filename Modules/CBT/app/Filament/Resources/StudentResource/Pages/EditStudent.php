<?php

namespace Modules\CBT\Filament\Resources\StudentResource\Pages;

use Modules\CBT\Filament\Resources\StudentResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Enums\Width;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 1. Update the linked User account
        $user = $this->record->user;
        
        $user_data = [
            'name' => $data['user_name'],
            'email' => $data['user_email'],
        ];

        if (filled($data['user_password'])) {
            $user_data['password'] = Hash::make($data['user_password']);
        }

        $user->update($user_data);

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
