<?php

use Illuminate\Support\Facades\Route;
use Modules\Pegawai\Http\Controllers\PegawaiController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('pegawais', PegawaiController::class)->names('pegawai');
});
