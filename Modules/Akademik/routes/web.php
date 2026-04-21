<?php

use Illuminate\Support\Facades\Route;
use Modules\Akademik\Http\Controllers\AkademikController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('akademiks', AkademikController::class)->names('akademik');
});
