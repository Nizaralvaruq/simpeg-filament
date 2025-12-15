<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('setup_koor')
                ->label('Buat Akun Koor (Instan)')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->label('Nama Koor')
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('email')
                        ->label('Email Login')
                        ->email()
                        ->required()
                        ->unique('users', 'email'),
                    \Filament\Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->required(),
                    \Filament\Forms\Components\Select::make('unit_id')
                        ->label('Unit yang Dipimpin')
                        ->options(\Modules\MasterData\Models\Unit::pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    // 1. Create User
                    $user = \App\Models\User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                    ]);

                    // 2. Assign Role
                    $user->assignRole('koor_jenjang');

                    // 3. Create Data Induk (Pegawai Profile)
                    $employee = \Modules\Kepegawaian\Models\DataInduk::create([
                        'user_id' => $user->id,
                        'nama' => $data['name'],
                        'status_kepegawaian' => 'Tetap', // Default
                    ]);

                    // 4. Link to Unit
                    $employee->units()->attach($data['unit_id']);

                    \Filament\Notifications\Notification::make()
                        ->title('Akun Koor Berhasil Dibuat')
                        ->body("User {$data['name']} siap digunakan untuk unit tersebut.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
