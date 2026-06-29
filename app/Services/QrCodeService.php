<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\Output\QRGdImagePNG;
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
                'outputType' => 'custom',
                'outputInterface' => QRGdImagePNG::class,
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

    /**
     * Generate a complete QR Card image containing the QR code, employee's name, and NPA/NIP.
     * 
     * @param string $token
     * @param string $name
     * @param string $nip
     * @param string $role
     * @return \GdImage|null
     */
    public function generateQrCard(string $token, string $name, string $nip, string $role = '')
    {
        $cardW = 800;
        $cardH = 500;

        // Create main image
        $card = imagecreatetruecolor($cardW, $cardH);

        // Define colors
        $white = imagecolorallocate($card, 255, 255, 255);
        $blueBg = imagecolorallocate($card, 0, 102, 204); // #0066cc
        $textDark = imagecolorallocate($card, 31, 41, 55); // #1f2937
        $textGray = imagecolorallocate($card, 156, 163, 175); // #9ca3af
        $textBlue = imagecolorallocate($card, 0, 102, 204); // #0066cc
        $greenBadgeBg = imagecolorallocate($card, 240, 253, 244); // #f0fdf4
        $greenBadgeBorder = imagecolorallocate($card, 187, 247, 208); // #bbf7d0
        $greenBadgeText = imagecolorallocate($card, 21, 128, 61); // #15803d
        $borderColor = imagecolorallocate($card, 229, 231, 235); // #e5e7eb
        $lineColor = imagecolorallocate($card, 243, 244, 246); // #f3f4f6

        // Fill background with white
        imagefilledrectangle($card, 0, 0, $cardW - 1, $cardH - 1, $white);

        // Draw left blue panel
        imagefilledrectangle($card, 0, 0, 300, $cardH - 1, $blueBg);

        // Cross-platform font configuration
        if (DIRECTORY_SEPARATOR === '\\') {
            // Local environment (Windows)
            // Use Windows system font (Arial) — GD compatible and avoids path space issues
            $fontBold = 'C:/Windows/Fonts/arialbd.ttf'; // Arial Bold
            $fontReg  = 'C:/Windows/Fonts/arial.ttf';   // Arial Regular
            
            if (!file_exists($fontBold)) {
                $fontBold = 'C:/Windows/Fonts/arial.ttf';
            }
        } else {
            // Hosting environment (Linux)
            // Use bundled DejaVu fonts
            $fontBold = public_path('fonts/DejaVuSans-Bold.ttf');
            $fontReg  = public_path('fonts/DejaVuSans.ttf');
        }

        $hasFonts = file_exists($fontReg) && (@imagettfbbox(8, 0, $fontReg, 'a') !== false);

        // Generate QR code image
        $qrImage = $this->generateQrImage($token);
        if ($qrImage) {
            $qrW = imagesx($qrImage);
            $qrH = imagesy($qrImage);

            // Resize and copy QR to card
            $targetQrSize = 220;
            $dstX = (300 - $targetQrSize) / 2;
            $dstY = (500 - $targetQrSize) / 2 - 20;

            imagecopyresampled($card, $qrImage, $dstX, $dstY, 0, 0, $targetQrSize, $targetQrSize, $qrW, $qrH);
            imagedestroy($qrImage);
        }

        // Draw "Scan untuk Absen"
        $scanText = "Scan untuk Absen";
        if ($hasFonts) {
            // Center the text
            $bbox = imagettfbbox(11, 0, $fontReg, $scanText);
            $textWidth = abs($bbox[4] - $bbox[0]);
            $textX = (300 - $textWidth) / 2;
            imagettftext($card, 11, 0, $textX, 400, $white, $fontReg, $scanText);
        } else {
            imagestring($card, 3, 90, 400, $scanText, $white);
        }

        // --- Right Section ---

        // 1. Status badge: "✓ KARTU QR PERMANEN"
        $badgeText = "✓ KARTU QR PERMANEN";
        // Let's draw badge background
        imagefilledrectangle($card, 340, 50, 540, 80, $greenBadgeBg);
        imagerectangle($card, 340, 50, 540, 80, $greenBadgeBorder);
        if ($hasFonts) {
            imagettftext($card, 9, 0, 355, 70, $greenBadgeText, $fontBold, $badgeText);
        } else {
            imagestring($card, 2, 355, 58, $badgeText, $greenBadgeText);
        }

        // 2. Name
        $nameFontSize = 20;
        if (strlen($name) > 30) {
            $nameFontSize = 14;
        } elseif (strlen($name) > 20) {
            $nameFontSize = 16;
        }

        if ($hasFonts) {
            imagettftext($card, 9, 0, 340, 130, $textGray, $fontBold, "NAMA KARYAWAN");
            imagettftext($card, $nameFontSize, 0, 340, 165, $textDark, $fontBold, $name);
        } else {
            imagestring($card, 2, 340, 115, "NAMA KARYAWAN", $textGray);
            imagestring($card, 5, 340, 135, $name, $textDark);
        }

        // 3. Jabatan
        if (!empty($role)) {
            $roleFontSize = 15;
            if (strlen($role) > 30) {
                $roleFontSize = 11;
            } elseif (strlen($role) > 20) {
                $roleFontSize = 13;
            }

            if ($hasFonts) {
                imagettftext($card, 9, 0, 340, 215, $textGray, $fontBold, "AMANAH / JABATAN");
                imagettftext($card, $roleFontSize, 0, 340, 245, $textDark, $fontBold, $role);
            } else {
                imagestring($card, 2, 340, 195, "AMANAH / JABATAN", $textGray);
                imagestring($card, 4, 340, 215, $role, $textDark);
            }
        }

        // 4. NPA / NIP
        if ($hasFonts) {
            imagettftext($card, 9, 0, 340, 305, $textGray, $fontBold, "NPA / NIP");
            imagettftext($card, 22, 0, 340, 345, $textBlue, $fontBold, $nip);
        } else {
            imagestring($card, 2, 340, 280, "NPA / NIP", $textGray);
            imagestring($card, 5, 340, 300, $nip, $textBlue);
        }

        // 5. Footer Line & Text
        imageline($card, 340, 410, 760, 410, $lineColor);
        if ($hasFonts) {
            imagettftext($card, 9, 0, 340, 440, $textGray, $fontReg, "Sistem Kepegawaian v2.0");
            imagettftext($card, 9, 0, 600, 440, $textGray, $fontReg, "QR Gen: Permanen");
        } else {
            imagestring($card, 2, 340, 420, "Sistem Kepegawaian v2.0", $textGray);
            imagestring($card, 2, 600, 420, "QR Gen: Permanen", $textGray);
        }

        // Draw card border
        imagerectangle($card, 0, 0, $cardW - 1, $cardH - 1, $borderColor);

        return $card;
    }
}

