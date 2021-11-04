<?php

use Illuminate\Support\Facades\Route;
use Modules\Pjt\Http\Controllers\VerifKajianPermohonanController;

Route::prefix('pjt')->middleware(['auth', 'restrict'])->group(function () {
	Route::prefix("verifikasi")->group(function () {
        Route::get('/', [VerifKajianPermohonanController::class, 'index']);
        Route::any('/ajax', [VerifKajianPermohonanController::class, 'ajax']);
        Route::get('/detail/{mohon_id}', [VerifKajianPermohonanController::class, 'detail']);
        Route::get('/edit', [VerifKajianPermohonanController::class, 'edit']);
        Route::post('/update', [VerifKajianPermohonanController::class, 'update']);
    });
});
