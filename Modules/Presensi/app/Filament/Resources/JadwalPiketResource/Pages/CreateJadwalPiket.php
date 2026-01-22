<?php

namespace Modules\Presensi\Filament\Resources\JadwalPiketResource\Pages;

use Modules\Presensi\Filament\Resources\JadwalPiketResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateJadwalPiket extends CreateRecord
{
    protected static string $resource = JadwalPiketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        if ($record->user) {
            $record->user->notify(
                \Filament\Notifications\Notification::make()
                    ->title('Jadwal Piket Baru')
                    ->body("Admin telah menjadwalkan Anda piket pada tanggal: " . $record->tanggal->format('d M Y'))
                    ->info()
                    ->toDatabase()
            );
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
