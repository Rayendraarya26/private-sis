<?php

use Illuminate\Support\Facades\Route;
use Modules\Pelanggan\Http\Controllers\AuditController;
use Modules\Pelanggan\Http\Controllers\BillingController;
use Modules\Pelanggan\Http\Controllers\JadwalController;
use Modules\Pelanggan\Http\Controllers\ProfilPerusahaanController;
use Modules\Pelanggan\Http\Controllers\SertifikasiDataController;
use Modules\Pelanggan\Http\Controllers\SertifikasiPermohonanController;

Route::prefix('pelanggan')->middleware(['auth', 'restrict'])->group(function () {
    Route::get("profil-perusahaan", [ProfilPerusahaanController::class, 'index']);

    Route::prefix("sertifikasi/permohonan")->group(function () {
        Route::get('/', [SertifikasiPermohonanController::class, 'index']);
        Route::get('/detail/{mohon_id}', [SertifikasiPermohonanController::class, 'detail']);
        Route::get('/track/{mohon_id}', [SertifikasiPermohonanController::class, 'track']);
        Route::any('/ajax', [SertifikasiPermohonanController::class, 'ajax']);
        Route::get('/edit/{mohon_id}', [SertifikasiPermohonanController::class, 'edit']);
        Route::post('/update', [SertifikasiPermohonanController::class, 'update']);
        Route::get('/create', [SertifikasiPermohonanController::class, 'create']);
        Route::post('/create', [SertifikasiPermohonanController::class, 'store']);
        Route::delete('/delete', [SertifikasiPermohonanController::class, 'destroy']);
    });

    Route::get("sertifikasi/data", [SertifikasiDataController::class, 'index']);

    Route::get("billing", [BillingController::class, 'index']);

    Route::get("jadwal", [JadwalController::class, 'index']);

    Route::get("audit", [AuditController::class, 'index']);
});
