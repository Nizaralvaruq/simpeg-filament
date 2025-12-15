<?php

use Illuminate\Support\Facades\Route;
use Modules\Kepegawaian\Http\Controllers\KepegawaianController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('kepegawaians', KepegawaianController::class)->names('kepegawaian');
});
