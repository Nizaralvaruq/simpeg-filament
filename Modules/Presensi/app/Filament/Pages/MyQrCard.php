<?php

namespace Modules\Presensi\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use App\Services\QrCodeService;

class MyQrCard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-qr-code';

    protected string $view = 'presensi::filament.pages.my-qr-card';

    protected static string | \UnitEnum | null $navigationGroup = 'Menu Saya';

    protected static ?string $navigationLabel = 'Kartu QR';

    protected static ?string $title = 'Kartu QR';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('download_png')
                ->label('Download PNG')
                ->icon('heroicon-o-photo')
                ->color('success')
                ->action(fn() => $this->downloadQrCode()),
        ];
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
        
        if ($user->hasRole('siswa')) {
            return $user->student?->nisn ?? '-';
        }
        
        return $user->employee?->nip ?? '-';
    }

    public function getUserJabatan(): string
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->hasRole('siswa')) {
            return 'Siswa ' . ($user->student?->class_name ?? '');
        }
        
        return $user->employee?->jabatan ?? 'Pegawai';
    }

    public function getQrGeneratedDate(): string
    {
        return 'Permanen (Berbasis NIP/NPA)';
    }

    public function getQrImageBase64(): string
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->qr_token;

        if (empty($token)) {
            // Return 1x1 transparent pixel
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        }

        try {
            $qrService = new QrCodeService();
            $image = $qrService->generateQrImage($token);

            if ($image) {
                ob_start();
                imagepng($image);
                $finalData = ob_get_clean();

                return 'data:image/png;base64,' . base64_encode($finalData);
            }
        } catch (\Throwable $e) {
            Log::error('MyQrCard SSR Error', ['msg' => $e->getMessage()]);
        }

        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
    }

    public function downloadQrCode()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->qr_token ?? '';

        $qrService = new QrCodeService();
        $image = $qrService->generateQrCard(
            $token,
            $this->getUserName(),
            $this->getUserNip() ?? '-',
            $this->getUserJabatan()
        );

        $qrCodeBinary = '';
        if ($image) {
            ob_start();
            imagepng($image);
            $qrCodeBinary = ob_get_clean();
            imagedestroy($image);
        }

        return response()->streamDownload(function () use ($qrCodeBinary) {
            echo $qrCodeBinary;
        }, 'Kartu-QR-' . ($this->getUserNip() ?? 'User') . '.png', [
            'Content-Type' => 'image/png',
        ]);
    }
}
