<?php

use Illuminate\Support\Facades\Route;
use Modules\CBT\Http\Controllers\CBTController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('cbts', CBTController::class)->names('cbt');
});
