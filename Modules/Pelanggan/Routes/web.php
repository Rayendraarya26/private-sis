<?php

use Illuminate\Support\Facades\Route;
use Modules\Pelanggan\Http\Controllers\AuditController;
use Modules\Pelanggan\Http\Controllers\BillingController;
use Modules\Pelanggan\Http\Controllers\JadwalController;
use Modules\Pelanggan\Http\Controllers\ProfilPerusahaanController;
use Modules\Pelanggan\Http\Controllers\SertifikasiDataController;
use Modules\Pelanggan\Http\Controllers\SertifikasiPermohonanController;

Route::prefix('pelanggan')->middleware(['auth', 'restrict'])->group(function () {
    Route::redirect('/', '/dashboard');

    Route::get("profil-perusahaan", [ProfilPerusahaanController::class, 'index']);

    Route::prefix("sertifikasi/data")->group(function () {
        Route::get("/", [SertifikasiDataController::class, 'index']);
        Route::get("/ajax", [SertifikasiDataController::class, 'ajax']);
    });

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
        Route::post('/approve-harga', [SertifikasiPermohonanController::class, 'approveHarga']);
    });

    Route::prefix("billing")->group(function () {
        Route::get("/", [BillingController::class, 'index']);
        Route::any("/ajax", [BillingController::class, 'ajax']);
        Route::get('download-invoice', [BillingController::class, 'downloadInvoice']);
        Route::get('upload/{billing_id}', [BillingController::class, 'upload']);
        Route::post('upload/{billing_id}', [BillingController::class, 'processUpload']);
    });

    Route::prefix("jadwal")->group(function () {
        Route::get("/", [JadwalController::class, 'index']);
        Route::any("/ajax", [JadwalController::class, 'ajax']);
        Route::get('approve/tanggal/{jadwal_id}', [JadwalController::class, 'approveTanggal']);
        Route::post('approve/tanggal/{jadwal_id}', [JadwalController::class, 'processApproveTanggal']);
        Route::get('approve/tim/{jadwal_id}', [JadwalController::class, 'approveTim']);
        Route::post('approve/tim/{jadwal_id}', [JadwalController::class, 'processApproveTim']);
    });

    Route::prefix("audit")->group(function () {
        Route::get("/", [AuditController::class, 'index']);
        Route::any("/ajax", [AuditController::class, 'ajax']);
        Route::get('/temuan-lks/{jadwal_id}', [AuditController::class, 'temuanLKS']);
        Route::get('/temuan-lks/{jadwal_id}/detail/{lks_id}', [AuditController::class, 'detailLKS']);
        Route::get('/temuan-lks/{jadwal_id}/perbaikan/{lks_id}', [AuditController::class, 'perbaikanLKS']);
        Route::post('/temuan-lks/{jadwal_id}/perbaikan/{lks_id}', [AuditController::class, 'processPerbaikanLKS']);
    });

});
