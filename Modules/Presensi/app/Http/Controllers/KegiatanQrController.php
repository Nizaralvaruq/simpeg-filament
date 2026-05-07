<?php

namespace Modules\Presensi\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\QrCodeService;
use Modules\Presensi\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KegiatanQrController extends Controller
{
    /**
     * Download QR Code PNG untuk kegiatan tertentu.
     */
    public function download(Request $request, int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Only authorized roles may download
        if (!$user || !$user->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit'])) {
            abort(403, 'Akses ditolak.');
        }

        $kegiatan = Kegiatan::findOrFail($id);
        $token    = 'KEGIATAN-' . $kegiatan->id;

        $qrService = new QrCodeService();
        $image     = $qrService->generateQrImage($token);

        if (!$image) {
            abort(500, 'Gagal generate QR Code.');
        }

        // Render QR to PNG buffer
        ob_start();
        imagepng($image);
        $pngData = ob_get_clean();
        imagedestroy($image);

        $filename = 'QR-Kegiatan-' . str($kegiatan->nama_kegiatan)->slug() . '-' . $kegiatan->id . '.png';

        return response($pngData, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Length'      => strlen($pngData),
            'Cache-Control'       => 'no-cache',
        ]);
    }
}
