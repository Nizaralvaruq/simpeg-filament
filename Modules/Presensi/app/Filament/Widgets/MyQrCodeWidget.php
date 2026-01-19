<?php

namespace Modules\Presensi\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class MyQrCodeWidget extends Widget
{
    protected string $view = 'presensi::filament.widgets.my-qr-code-widget';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function getUserData(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->employee || !$user->employee->nip) {
            return [
                'has_nip' => false,
                'name' => $user->name ?? 'User',
                'qr_code' => null,
                'nip' => null
            ];
        }

        // Generate QR
        $nip = $user->employee->nip;

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($nip);

        return [
            'has_nip' => true,
            'name' => $user->name,
            'jabatan' => $user->employee->jabatan ?? 'Staff',
            'nip' => $nip,
            'qr_code' => $qrSvg,
            'avatar' => $user->getFilamentAvatarUrl()
        ];
    }
}
