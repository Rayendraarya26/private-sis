<?php


use Illuminate\Support\Facades\Route;
use Modules\Marketing\Http\Controllers\MarketingController;
use Modules\Marketing\Http\Controllers\VerifikasiPermohonanController;

Route::prefix('marketing')->group(function() {
    Route::get('/', [MarketingController::class, 'index']);

	Route::prefix("verifikasi-permohonan")->group(function () {
        Route::get('/', [VerifikasiPermohonanController::class, 'index']);
        Route::any('/ajax', [VerifikasiPermohonanController::class, 'ajax']);
        Route::get('/detail/{mohon_id}', [VerifikasiPermohonanController::class, 'detail']);
        Route::get('/edit', [VerifikasiPermohonanController::class, 'edit']);
        Route::post('/update', [VerifikasiPermohonanController::class, 'update']);
    });
});
