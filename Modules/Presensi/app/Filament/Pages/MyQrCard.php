<?php

namespace Modules\Presensi\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
                ->action(fn() => $this->downloadQrCode()),
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

    public function getQrCodeSvg(): string
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->qr_token;

        Log::info('MyQrCard Debug', [
            'user_id' => $user->id,
            'token' => $token,
            'token_length' => strlen($token ?? ''),
        ]);

        if (empty($token)) {
            Log::warning('MyQrCard: Token is empty');
            return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48dGV4dCB4PSIxMCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgZmlsbD0icmVkIj5ObyBRUiBUb2tlbjwvdGV4dD48L3N2Zz4=';
        }

        try {
            $options = new \chillerlan\QRCode\QROptions([
                'version'      => 5,
                'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG,
                'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_L,
                'addQuietzone' => false,
            ]);

            $svg = (new \chillerlan\QRCode\QRCode($options))->render($token);

            // Ensure responsive attributes
            $svg = str_replace('<svg ', '<svg width="100%" height="100%" ', $svg);

            $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);
            Log::info('MyQrCard: SVG Generated', ['data_uri_start' => substr($dataUri, 0, 50)]);

            return $dataUri;
        } catch (\Throwable $e) {
            Log::error('MyQrCard Error', ['message' => $e->getMessage()]);
            return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48dGV4dCB4PSIxMCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMCIgZmlsbD0icmVkIj5FcnJvcjwvdGV4dD48L3N2Zz4=';
        }
    }

    public function downloadQrCode()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $options = new \chillerlan\QRCode\QROptions([
            'version'    => 5,
            'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => \chillerlan\QRCode\QRCode::ECC_L,
            'scale'      => 10,
            'imageBase64' => false,
        ]);

        $qrcode = (new \chillerlan\QRCode\QRCode($options))->render($user->qr_token ?? '');

        return response()->streamDownload(function () use ($qrcode) {
            echo $qrcode;
        }, 'QR-Code-' . ($user->employee?->nip ?? 'User') . '.png', [
            'Content-Type' => 'image/png',
        ]);
    }
}
