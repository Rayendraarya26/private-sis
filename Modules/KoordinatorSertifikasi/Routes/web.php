<?php

use Illuminate\Support\Facades\Route;
use Modules\KoordinatorSertifikasi\Http\Controllers\VerifLapLengkapController;

Route::prefix('koordinatorsertifikasi')->middleware(['auth', 'restrict'])->group(function () {
    Route::prefix('verif')->group(function(){
        Route::get('/', [VerifLapLengkapController::class, 'index']);
    });
});
