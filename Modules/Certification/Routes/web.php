<?php

use Illuminate\Support\Facades\Route;
use Modules\Certification\Http\Controllers\PermohonanSertifikasiController;
use Modules\Certification\Http\Controllers\DataSertifikatController;

Route::prefix('sertifikasi')->middleware(['auth', 'restrict'])->group(function () {
    Route::get('/data-sertifikat', [DataSertifikatController::class, 'index']);

    Route::prefix("permohonan-sertifikasi")->group(function () {
        Route::get('/', [PermohonanSertifikasiController::class, 'index']);
        Route::get('/ajax', [PermohonanSertifikasiController::class, 'ajax']);
        Route::get('/create', [PermohonanSertifikasiController::class, 'create']);
        Route::get('/store', [PermohonanSertifikasiController::class, 'store']);
    });
});
