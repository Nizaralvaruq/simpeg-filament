<?php

namespace Modules\Presensi\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class MyQrCard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-qr-code';

    protected string $view = 'presensi::filament.pages.my-qr-card';

    protected static string | \UnitEnum | null $navigationGroup = 'Menu Saya';

    protected static ?string $navigationLabel = 'Kartu QR Saya';

    protected static ?string $title = 'Kartu QR Saya';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('regenerate')
                ->label('Regenerate QR')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Regenerate QR Code?')
                ->modalDescription('Kode QR lama Anda tidak akan bisa digunakan lagi untuk absensi.')
                ->action(fn() => $this->regenerateQr()),

            \Filament\Actions\Action::make('download_png')
                ->label('Download PNG')
                ->icon('heroicon-o-photo')
                ->color('success')
                ->action(fn() => $this->dispatch('download-qr-png')),
        ];
    }

    public function regenerateQr(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->generateQrToken();

        Notification::make()
            ->title('QR Code berhasil diperbarui')
            ->success()
            ->send();
    }

    public function getQrContent(): ?string
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->qr_token;
    }

    public function getUserName(): string
    {
        return Auth::user()->name;
    }

    public function getUserNip(): ?string
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->employee?->nip ?? '-';
    }

    public function getUserJabatan(): string
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->employee?->jabatan ?? 'Pegawai';
    }

    public function getQrGeneratedDate(): string
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->qr_token_generated_at
            ? $user->qr_token_generated_at->format('d M Y H:i')
            : 'Permanent (NIP Based)';
    }
}
