<?php

use Illuminate\Support\Facades\Route;
use Modules\CBT\Http\Controllers\CBTController;

Route::middleware(['auth', 'verified'])->prefix('cbt')->name('cbt.')->group(function () {
    Route::get('/', [CBTController::class, 'index'])->name('index');
    Route::get('/{exam}/token', [CBTController::class, 'showTokenGateway'])->name('token');
    Route::post('/{exam}/start', [CBTController::class, 'startExam'])->name('start');
    Route::get('/{exam}/play', [CBTController::class, 'play'])->name('play');
});
