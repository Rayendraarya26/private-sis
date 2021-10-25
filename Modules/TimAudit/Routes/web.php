<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Support\Facades\Route;
use Modules\TimAudit\Http\Controllers\AuDaftarHadirController;
use Modules\TimAudit\Http\Controllers\AuDaftarPeriksaController;
use Modules\TimAudit\Http\Controllers\AuLapLengkapController;
use Modules\TimAudit\Http\Controllers\AuLapRingkasController;
use Modules\TimAudit\Http\Controllers\AuLksController;
use Modules\TimAudit\Http\Controllers\AuLogBookController;
use Modules\TimAudit\Http\Controllers\AuPengajuanKomiteController;
use Modules\TimAudit\Http\Controllers\AuTahap1Controller;
use Modules\TimAudit\Http\Controllers\AuUploadJadwalController;
use Modules\TimAudit\Http\Controllers\KomiteLembarPeriksaController;
use Modules\TimAudit\Http\Controllers\KomiteRekomPersetujuanController;
use Modules\TimAudit\Http\Controllers\PersetujuanTimAuditController;
use Modules\TimAudit\Http\Controllers\PersetujuanTimKomiteController;
use Modules\TimAudit\Http\Controllers\PpcLaporanController;
use Modules\TimAudit\Http\Controllers\PpcLogBookController;

Route::prefix('timaudit')->middleware(['auth', 'restrict'])->group(function () {
    // ============================== Persetujuan TIM ==============================
    Route::prefix("persetujuan-tim")->group(function () {
        Route::prefix("auditor")->group(function () {
            Route::get('/', [PersetujuanTimAuditController::class, 'index']);
            Route::get('/ajax', [PersetujuanTimAuditController::class, 'ajax']);
            Route::get('/edit', [PersetujuanTimAuditController::class, 'edit']);
            Route::post('/update', [PersetujuanTimAuditController::class, 'update']);
        });

        Route::prefix("komite")->group(function () {
            Route::get('/', [PersetujuanTimKomiteController::class, 'index']);
            Route::get('/ajax', [PersetujuanTimKomiteController::class, 'ajax']);
        });
    });


    // ============================== Auditor ==============================
    Route::prefix("auditor")->group(function () {
        Route::prefix("upload-jadwal")->group(function () {
            Route::get('/', [AuUploadJadwalController::class, 'index']);
            Route::get('/ajax', [AuUploadJadwalController::class, 'ajax']);
            Route::get('/edit', [AuUploadJadwalController::class, 'edit']);
            Route::post('/update', [AuUploadJadwalController::class, 'update']);
        });

        Route::prefix("tahap1")->group(function () {
            Route::get('/', [AuTahap1Controller::class, 'index']);
            Route::get('/ajax', [AuTahap1Controller::class, 'ajax']);
        });

        Route::prefix("daftar-periksa")->group(function () {
            Route::get('/', [AuDaftarPeriksaController::class, 'index']);
            Route::get('/ajax', [AuDaftarPeriksaController::class, 'ajax']);
        });
        Route::prefix("lks")->group(function () {
            Route::get('/', [AuLksController::class, 'index']);
            Route::get('/ajax', [AuLksController::class, 'ajax']);
        });

        Route::prefix("laporan-ringkas")->group(function () {
            Route::get('/', [AuLapRingkasController::class, 'index']);
            Route::get('/ajax', [AuLapRingkasController::class, 'ajax']);
        });

        Route::prefix("laporan-lengkap")->group(function () {
            Route::get('/', [AuLapLengkapController::class, 'index']);
            Route::get('/ajax', [AuLapLengkapController::class, 'ajax']);
        });

        Route::prefix("daftar-hadir")->group(function () {
            Route::get('/', [AuDaftarHadirController::class, 'index']);
            Route::get('/ajax', [AuDaftarHadirController::class, 'ajax']);
        });

        Route::prefix("log-book")->group(function () {
            Route::get('/', [AuLogBookController::class, 'index']);
            Route::get('/ajax', [AuLogBookController::class, 'ajax']);
        });

        Route::prefix("pengajuan-komite")->group(function () {
            Route::get('/', [AuPengajuanKomiteController::class, 'index']);
            Route::get('/ajax', [AuPengajuanKomiteController::class, 'ajax']);
        });
    });


    // ============================== Komite ==============================
    Route::prefix("komite")->group(function () {
        Route::prefix("rekomendasi-persetujuan")->group(function () {
            Route::get('/', [KomiteRekomPersetujuanController::class, 'index']);
            Route::get('/ajax', [KomiteRekomPersetujuanController::class, 'ajax']);
        });

        Route::prefix("lembar-periksa")->group(function () {
            Route::get('/', [KomiteLembarPeriksaController::class, 'index']);
            Route::get('/ajax', [KomiteLembarPeriksaController::class, 'ajax']);
        });
    });

    
    // ============================== Komite ==============================
    Route::prefix("ppc")->group(function () {
        Route::prefix("laporan")->group(function () {
            Route::get('/', [PpcLaporanController::class, 'index']);
            Route::get('/ajax', [PpcLaporanController::class, 'ajax']);
        });

        Route::prefix("log-book")->group(function () {
            Route::get('/', [PpcLogBookController::class, 'index']);
            Route::get('/ajax', [PpcLogBookController::class, 'ajax']);
        });
    });
});
