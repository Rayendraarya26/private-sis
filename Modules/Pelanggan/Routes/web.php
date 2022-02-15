<?php

use Illuminate\Support\Facades\Route;
use Modules\Pelanggan\Http\Controllers\BillingController;
use Modules\Pelanggan\Http\Controllers\ProfilPerusahaanController;
use Modules\Pelanggan\Http\Controllers\SertifikasiDataController;
use Modules\Pelanggan\Http\Controllers\SertifikasiPermohonanController;
use Modules\Pelanggan\Http\Controllers\Tahap1JadwalController;
use Modules\Pelanggan\Http\Controllers\Tahap1PerbaikanController;
use Modules\Pelanggan\Http\Controllers\Tahap1PersetujuanController;
use Modules\Pelanggan\Http\Controllers\Tahap2JadwalController;
use Modules\Pelanggan\Http\Controllers\Tahap2PerbaikanController;
use Modules\Pelanggan\Http\Controllers\Tahap2PersetujuanController;

Route::prefix('pelanggan')->middleware(['auth'])->group(function () {
    Route::redirect('/', '/dashboard');

    Route::get("profil-perusahaan", [ProfilPerusahaanController::class, 'index']);

    Route::prefix("sertifikasi/data")->group(function () {
        Route::get("/", [SertifikasiDataController::class, 'index']);
        Route::get("/ajax", [SertifikasiDataController::class, 'ajax']);
        Route::get('/preview', [SertifikasiDataController::class, 'preview']);
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
        Route::get("/", [Tahap2JadwalController::class, 'index']);
        Route::any("/ajax", [Tahap2JadwalController::class, 'ajax']);
        Route::get('approve/tanggal/{jadwal_id}', [Tahap2JadwalController::class, 'approveTanggal']);
        Route::post('approve/tanggal/{jadwal_id}', [Tahap2JadwalController::class, 'processApproveTanggal']);
        Route::get('approve/tim/{jadwal_id}', [Tahap2JadwalController::class, 'approveTim']);
        Route::post('approve/tim/{jadwal_id}', [Tahap2JadwalController::class, 'processApproveTim']);
    });

    Route::prefix('tahap1')->group(function () {
        Route::prefix("jadwal")->group(function () {
            Route::get("/", [Tahap1JadwalController::class, 'index']);
            Route::get("/detail/{aud_thp1_id}", [Tahap1JadwalController::class, 'detail']);
            Route::any("/ajax", [Tahap1JadwalController::class, 'ajax']);
        });

        Route::prefix("persetujuan-temuan")->group(function () {
            Route::get("/", [Tahap1PersetujuanController::class, 'index']);
            Route::any("/ajax", [Tahap1PersetujuanController::class, 'ajax']);
            Route::get("/detail/{aud_thp1_id}", [Tahap1PersetujuanController::class, 'detail']);
            Route::get("/cetak/{aud_thp1_id}/tinjauan", [Tahap1PersetujuanController::class, 'cetakTinjauan']);
            Route::get("/cetak/{aud_thp1_id}/laporan", [Tahap1PersetujuanController::class, 'cetakLaporan']);
            Route::post("/approve-temuan", [Tahap1PersetujuanController::class, 'approveTemuan']);
        });

        Route::prefix("perbaikan-temuan")->group(function () {
            Route::get("/", [Tahap1PerbaikanController::class, 'index']);
            Route::any("/ajax", [Tahap1PerbaikanController::class, 'ajax']);
            Route::get("/revisi/{enc_aud_thp1_id}", [Tahap1PerbaikanController::class, 'revisi']);
            Route::post("/revisi/{enc_aud_thp1_id}", [Tahap1PerbaikanController::class, 'processRevisi']);
        });
    });

    Route::prefix('tahap2')->group(function () {
        Route::prefix("jadwal")->group(function () {
            Route::get("/", [Tahap2JadwalController::class, 'index']);
            Route::any("/ajax", [Tahap2JadwalController::class, 'ajax']);
            Route::get("/approve/tanggal/{jadw_id}", [Tahap2JadwalController::class, 'approveTanggal']);
            Route::post("/approve/tanggal/{jadw_id}", [Tahap2JadwalController::class, 'processApproveTanggal']);
            Route::get("/approve/tim/{jadw_id}", [Tahap2JadwalController::class, 'approveTim']);
            Route::post("/approve/tim/{jadw_id}", [Tahap2JadwalController::class, 'processApproveTim']);
        });

        Route::prefix("persetujuan-temuan")->group(function () {
            Route::get("/", [Tahap2PersetujuanController::class, 'index']);
            Route::any("/ajax", [Tahap2PersetujuanController::class, 'ajax']);
            Route::get("/detail/{jadw_id}", [Tahap2PersetujuanController::class, 'detail']);
            Route::post("/approve/temuan", [Tahap2PersetujuanController::class, 'approveTemuan']);
            Route::get("/cetak/{jadw_id}/{type}", [Tahap2PersetujuanController::class, 'cetak'])->where('type', 'notulen|lap-ringkas|daftar-hadir|logbook|lks');
        });

        Route::prefix("perbaikan-temuan")->group(function () {
            Route::get("/", [Tahap2PerbaikanController::class, 'index']);
            Route::any("/ajax", [Tahap2PerbaikanController::class, 'ajax']);
            Route::get("/cetak/{jadw_id}/{type}", [Tahap2PerbaikanController::class, 'cetak'])->where('type', 'notulen|lap-ringkas|daftar-hadir|logbook|lks');
            Route::get('/temuan-lks/{jadwal_id}', [Tahap2PerbaikanController::class, 'temuanLKS']);
            Route::get('/temuan-lks/{jadwal_id}/detail', [Tahap2PerbaikanController::class, 'detailAllLKS']);
            Route::post('/temuan-lks/{jadwal_id}/send-to-auditor', [Tahap2PerbaikanController::class, 'submitLKS']);
            Route::post('/temuan-lks/{jadwal_id}/save-perbaikan-text/{lks_id}', [Tahap2PerbaikanController::class, 'savePerbaikanText']);
            Route::post('/temuan-lks/{jadwal_id}/save-perbaikan-file/{lks_id}', [Tahap2PerbaikanController::class, 'savePerbaikanFile']);
        });
    });
});
