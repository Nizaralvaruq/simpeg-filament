<?php

use Illuminate\Support\Facades\Route;
use Modules\Kepegawaian\Http\Controllers\KepegawaianController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('kepegawaians', KepegawaianController::class)->names('kepegawaian');
});
