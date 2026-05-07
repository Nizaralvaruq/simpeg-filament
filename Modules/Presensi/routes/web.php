<?php

use Illuminate\Support\Facades\Route;
use Modules\Kepegawaian\Http\Controllers\KepegawaianController;
use Modules\Presensi\Http\Controllers\KegiatanQrController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('kepegawaians', KepegawaianController::class)->names('kepegawaian');

    // QR Code download untuk kegiatan
    Route::get('/kegiatan/{id}/qr/download', [KegiatanQrController::class, 'download'])
        ->name('kegiatan.qr.download');
});
