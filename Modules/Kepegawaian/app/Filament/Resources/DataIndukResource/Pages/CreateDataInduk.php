<?php

namespace Modules\Kepegawaian\Filament\Resources\DataIndukResource\Pages;

use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\Hash;
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

            $user->assignRole('staff');

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
        // sinkron jabatan dari riwayat
        if (! empty($data['riwayatJabatans'])) {
            $latest = collect($data['riwayatJabatans'])
                ->filter(fn ($r) => ! empty($r['tanggal']) && ! empty($r['nama_jabatan']))
                ->sortByDesc('tanggal')
                ->first();

            if ($latest) {
                $data['jabatan'] = $latest['nama_jabatan'];
            }
        }

        // sinkron golongan dari riwayat
        if (! empty($data['riwayatGolongans'])) {
            $latest = collect($data['riwayatGolongans'])
                ->filter(fn ($r) => ! empty($r['tanggal']) && ! empty($r['golongan_id']))
                ->sortByDesc('tanggal')
                ->first();

            if ($latest) {
                $data['golongan_id'] = $latest['golongan_id'];
            }
        }

        return $data;
    }

}
