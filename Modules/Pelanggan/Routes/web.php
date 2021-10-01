<?php

use Illuminate\Support\Facades\Route;
use Modules\Pelanggan\Http\Controllers\SertifikasiPermohonanController;

Route::prefix('pelanggan')->middleware(['auth'])->group(function () {
    Route::prefix("sertifikasi/permohonan")->group(function () {
        Route::get('/', [SertifikasiPermohonanController::class, 'index']);
        Route::any('/ajax', [SertifikasiPermohonanController::class, 'ajax']);
        Route::get('/create', [SertifikasiPermohonanController::class, 'create']);
        Route::post('/create', [SertifikasiPermohonanController::class, 'store']);
        Route::delete('/delete', [SertifikasiPermohonanController::class, 'destroy']);
    });
});
