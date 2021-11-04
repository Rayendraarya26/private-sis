<?php


use Illuminate\Support\Facades\Route;
use Modules\Marketing\Http\Controllers\MarketingController;
use Modules\Marketing\Http\Controllers\UploadKajianPermohonanController;
use Modules\Marketing\Http\Controllers\VerifikasiPermohonanController;

Route::prefix('marketing')->middleware(['auth', 'restrict'])->group(function() {
    Route::get('/', [MarketingController::class, 'index']);

	Route::prefix("verifikasi-permohonan")->group(function () {
        Route::get('/', [VerifikasiPermohonanController::class, 'index']);
        Route::any('/ajax', [VerifikasiPermohonanController::class, 'ajax']);
        Route::get('/detail/{mohon_id}', [VerifikasiPermohonanController::class, 'detail']);
        Route::get('/edit', [VerifikasiPermohonanController::class, 'edit']);
        Route::post('/update', [VerifikasiPermohonanController::class, 'update']);
    });

    Route::prefix("kajian-permohonan")->group(function () {
        Route::get('/', [UploadKajianPermohonanController::class, 'index']);
        Route::any('/ajax', [UploadKajianPermohonanController::class, 'ajax']);
        Route::get('/detail/{mohon_id}', [UploadKajianPermohonanController::class, 'detail']);
        Route::get('/edit', [UploadKajianPermohonanController::class, 'edit']);
        Route::post('/update', [UploadKajianPermohonanController::class, 'update']);
    });
});
