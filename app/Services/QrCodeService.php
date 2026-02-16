<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    /**
     * Generate a robust QR Code image (GD Image Resource) with Logo and No Border.
     * 
     * @param string $token
     * @return \GdImage|null
     */
    public function generateQrImage(string $token)
    {
        if (empty($token)) {
            return null;
        }

        try {
            $options = new QROptions([
                'version'    => 5,
                'outputType' => QROutputInterface::GDIMAGE_PNG,
                'eccLevel'   => EccLevel::H,
                'scale'      => 10,
                'outputBase64' => false,
                'addQuietzone' => true,
                'quietzoneSize' => 4,
                'returnResource' => false, // Return string for manual GD creation
            ]);

            $qrCode = new QRCode($options);
            $pngData = $qrCode->render($token);

            // Create GD Image from string (Robust Method)
            $image = @imagecreatefromstring($pngData);

            if ($image) {
                // Merge Logo if exists
                $this->mergeLogo($image);

                return $image;
            }
        } catch (\Throwable $e) {
            Log::error('QrCodeService Error', ['msg' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Merge logo into the center of the QR code.
     * 
     * @param \GdImage $image
     */
    protected function mergeLogo($image): void
    {
        $logoPath = public_path('images/logo1.png');
        if (!file_exists($logoPath)) {
            return;
        }

        $logo = @imagecreatefrompng($logoPath);
        if (!$logo) {
            return;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $logoW = imagesx($logo);
        $logoH = imagesy($logo);

        imagealphablending($image, true);

        // Size logo to 20% of QR size
        $targetW = $width * 0.2;
        $targetH = $logoH * ($targetW / $logoW);

        $dstX = ($width - $targetW) / 2;
        $dstY = ($height - $targetH) / 2;

        // White background circle for logo
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefilledellipse($image, $width / 2, $height / 2, $targetW + 10, $targetH + 10, $white);

        // Copy logo
        imagecopyresampled($image, $logo, $dstX, $dstY, 0, 0, $targetW, $targetH, $logoW, $logoH);
    }
}
