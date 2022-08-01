<?php

use Illuminate\Support\Facades\Route;
use Modules\Pjt\Http\Controllers\VerifKajianPermohonanController;
use Modules\Pjt\Http\Controllers\PembatalanPermohonanController;

Route::prefix('pjt')->middleware(['auth', 'restrict'])->group(function () {
	Route::prefix("verifikasi")->group(function () {
        Route::get('/', [VerifKajianPermohonanController::class, 'index']);
        Route::any('/ajax', [VerifKajianPermohonanController::class, 'ajax']);
        Route::get('/detail/{mohon_id}', [VerifKajianPermohonanController::class, 'detail']);
        Route::get('/edit', [VerifKajianPermohonanController::class, 'edit']);
        Route::post('/update', [VerifKajianPermohonanController::class, 'update']);
    });
	
	Route::prefix("verif_cancel")->group(function () {
        Route::get('/', [PembatalanPermohonanController::class, 'index']);
        Route::any('/ajax', [PembatalanPermohonanController::class, 'ajax']);
        Route::get('/detail/{mohon_id}', [PembatalanPermohonanController::class, 'detail']);
        Route::get('/proses_cancel/{mohon_id}', [PembatalanPermohonanController::class, 'procesCancel']);
    });
});
