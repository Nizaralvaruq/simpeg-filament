<?php

use Illuminate\Support\Facades\Route;
use Modules\PenilaianKinerja\Http\Controllers\PenilaianKinerjaController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('penilaiankinerjas', PenilaianKinerjaController::class)->names('penilaiankinerja');
});
