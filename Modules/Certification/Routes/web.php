<?php

use Illuminate\Support\Facades\Route;
use Modules\Certification\Http\Controllers\DataSertifikatController;
use Modules\Certification\Http\Controllers\PermohonanSertifikasiController;

Route::prefix('sertifikasi')->middleware(['auth', 'restrict'])->group(function () {
    Route::get('/data-sertifikat', [DataSertifikatController::class, 'index']);

    Route::prefix("permohonan-sertifikasi")->group(function () {
        Route::get('/', [PermohonanSertifikasiController::class, 'index']);
        Route::any('/ajax', [PermohonanSertifikasiController::class, 'ajax']);
        Route::get('/create', [PermohonanSertifikasiController::class, 'create']);
        Route::get('/store', [PermohonanSertifikasiController::class, 'store']);
    });
});
