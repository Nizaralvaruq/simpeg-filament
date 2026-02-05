<?php

namespace Modules\Kepegawaian\Filament\Resources\DataIndukResource\Pages;

use Dflydev\DotAccessData\Data;
use Filament\Resources\Pages\EditRecord;
use Modules\Kepegawaian\Filament\Resources\DataIndukResource;

use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use App\Models\User;

class EditDataInduk extends EditRecord
{
    protected static string $resource = DataIndukResource::class;

    protected function afterSave(): void
    {
        $data = $this->form->getRawState();
        $record = $this->record;

        // Jika sudah punya user, update datanya
        if ($record->user) {
            $user = $record->user;
            $updateData = [];

            if (!empty($data['email']) && $data['email'] !== $user->email) {
                $updateData['email'] = $data['email'];
            }

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            if (!empty($updateData)) {
                $user->update($updateData);

                Notification::make()
                    ->title('Akun Login Berhasil Diperbarui')
                    ->success()
                    ->send();
            }

            // Sync roles if provided
            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }
        }
        // Jika belum punya user, dan email + password diisi, buat baru
        elseif (!empty($data['email']) && !empty($data['password'])) {
            $user = User::create([
                'name' => $record->nama,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            if (!empty($data['roles'])) {
                $user->assignRole($data['roles']);
            } else {
                $user->assignRole('staff');
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Jika tetap, bersihkan riwayat jabatan di DB
        if (($data['pindah_tugas'] ?? 'tetap') === 'tetap') {
            $this->record->riwayatJabatans()->delete();
        }

        return $data;
    }
}
