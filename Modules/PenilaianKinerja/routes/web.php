<?php

use Illuminate\Support\Facades\Route;
use Modules\PenilaianKinerja\Http\Controllers\PenilaianKinerjaController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('penilaiankinerjas', PenilaianKinerjaController::class)->names('penilaiankinerja');
});
