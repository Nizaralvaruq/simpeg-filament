<?php

namespace Modules\Presensi\Filament\Resources\AbsensiResource\Pages;

use Modules\Presensi\Filament\Resources\AbsensiResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Modules\Presensi\Models\Absensi;

class CreateAbsensi extends CreateRecord
{
    protected static string $resource = AbsensiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        // Enforce user_id for non-admins
        if ($user instanceof \App\Models\User && !$user->hasRole('super_admin')) {
            $data['user_id'] = $user->id;
            $data['tanggal'] = now()->toDateString();
        }

        // Check for duplicates
        $exists = Absensi::where('user_id', $data['user_id'])
            ->whereDate('tanggal', $data['tanggal'])
            ->exists();

        if ($exists) {
            Notification::make()
                ->title('Absensi Gagal')
                ->body('Anda sudah melakukan absensi hari ini.')
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
