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
use Modules\TimAudit\Http\Controllers\AuUploadJadwalTahap1Controller;
use Modules\TimAudit\Http\Controllers\KomiteLembarPeriksaController;
use Modules\TimAudit\Http\Controllers\KomiteRekomPersetujuanController;
use Modules\TimAudit\Http\Controllers\PersetujuanTimAuditController;
use Modules\TimAudit\Http\Controllers\PpcLaporanController;
use Modules\TimAudit\Http\Controllers\PpcLogBookController;

// Route::prefix('timaudit')->middleware(['auth', 'restrict'])->group(function () {
Route::prefix('timaudit')->middleware(['auth'])->group(function () {
    // ============================== Persetujuan TIM ==============================
    Route::prefix("persetujuan-tim")->group(function () {
        Route::prefix("auditor")->group(function () {
            Route::get('/', [PersetujuanTimAuditController::class, 'index']);
            Route::get('/ajax', [PersetujuanTimAuditController::class, 'ajax']);
            Route::get('/edit', [PersetujuanTimAuditController::class, 'edit']);
            Route::post('/update', [PersetujuanTimAuditController::class, 'update']);
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

		Route::prefix("upload-jadwal-tahap1")->group(function () {
            Route::get('/', [AuUploadJadwalTahap1Controller::class, 'index']);
            Route::get('/ajax', [AuUploadJadwalTahap1Controller::class, 'ajax']);
            Route::get('/edit', [AuUploadJadwalTahap1Controller::class, 'edit']);
            Route::post('/update', [AuUploadJadwalTahap1Controller::class, 'update']);
        });


        Route::prefix("tahap1")->group(function () {
            Route::get('/', [AuTahap1Controller::class, 'index']);
            Route::any('/ajax', [AuTahap1Controller::class, 'ajax']);
			Route::get('/edit', [AuTahap1Controller::class, 'edit']);
			Route::post('/update', [AuTahap1Controller::class, 'update']);
			Route::delete('/delete', [AuTahap1Controller::class, 'destroy']);
			Route::get('/print', [AuTahap1Controller::class, 'print']);
        });

        Route::prefix("daftar-periksa")->group(function () {
            Route::get('/', [AuDaftarPeriksaController::class, 'index']);
            Route::get('/ajax', [AuDaftarPeriksaController::class, 'ajax']);
            Route::get('/edit', [AuDaftarPeriksaController::class, 'edit']);
            Route::post('/update', [AuDaftarPeriksaController::class, 'update']);
        });
        Route::prefix("lks")->group(function () {
            Route::get('/', [AuLksController::class, 'index']);
            Route::get('/ajax', [AuLksController::class, 'ajax']);
            Route::get('/temuan/{jadw_id}', [AuLksController::class, 'temuan']);
            Route::get('/temuan/{jadw_id}/tambah', [AuLksController::class, 'addTemuan']);
            Route::post('/temuan/{jadw_id}/tambah', [AuLksController::class, 'storeTemuan']);
            Route::get('/temuan/{jadw_id}/edit/{lks_id}', [AuLksController::class, 'editTemuan']);
            Route::get('/temuan/{jadw_id}/detail/{lks_id}', [AuLksController::class, 'detailTemuan']);
            Route::delete('/temuan/{jadw_id}/delete/{lks_id}', [AuLksController::class, 'deleteTemuan']);
            Route::post('/temuan/{jadw_id}/verif/{lks_id}', [AuLksController::class, 'verifTemuan']);
        });

        Route::prefix("laporan-ringkas")->group(function () {
            Route::get('/', [AuLapRingkasController::class, 'index']);
            Route::get('/ajax', [AuLapRingkasController::class, 'ajax']);
            Route::get('/laporan', [AuLapRingkasController::class, 'laporan']);
            Route::post('/laporan', [AuLapRingkasController::class, 'processLaporan']);
        });

        Route::prefix("laporan-lengkap")->group(function () {
            Route::get('/', [AuLapLengkapController::class, 'index']);
            Route::get('/ajax', [AuLapLengkapController::class, 'ajax']);
            Route::get('/laporan', [AuLapLengkapController::class, 'laporan']);
            Route::post('/laporan', [AuLapLengkapController::class, 'processLaporan']);
        });

        Route::prefix("daftar-hadir")->group(function () {
            Route::get('/', [AuDaftarHadirController::class, 'index']);
            Route::get('/ajax', [AuDaftarHadirController::class, 'ajax']);
            Route::get('/unggah/{jadw_id}', [AuDaftarHadirController::class, 'unggah']);
            Route::post('/unggah/{jadw_id}', [AuDaftarHadirController::class, 'storeUnggah']);
        });

        Route::prefix("log-book")->group(function () {
            Route::get('/', [AuLogBookController::class, 'index']);
            Route::get('/ajax', [AuLogBookController::class, 'ajax']);
            Route::get('/edit', [AuLogBookController::class, 'edit']);
            Route::post('/update', [AuLogBookController::class, 'update']);
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
            Route::any('/ajax', [KomiteRekomPersetujuanController::class, 'ajax']);
            Route::get('/edit', [KomiteRekomPersetujuanController::class, 'edit']);
            Route::post('/update', [KomiteRekomPersetujuanController::class, 'update']);
        });

        Route::prefix("lembar-periksa")->group(function () {
            Route::get('/', [KomiteLembarPeriksaController::class, 'index']);
            Route::any('/ajax', [KomiteLembarPeriksaController::class, 'ajax']);
            Route::get('/edit', [KomiteLembarPeriksaController::class, 'edit']);
            Route::post('/update', [KomiteLembarPeriksaController::class, 'update']);
        });
    });


    // ============================== Komite ==============================
    Route::prefix("ppc")->group(function () {
        Route::prefix("laporan")->group(function () {
            Route::get('/', [PpcLaporanController::class, 'index']);
            Route::get('/ajax', [PpcLaporanController::class, 'ajax']);
            Route::get('/edit', [PpcLaporanController::class, 'edit']);
            Route::post('/update', [PpcLaporanController::class, 'update']);
        });

        Route::prefix("log-book")->group(function () {
            Route::get('/', [PpcLogBookController::class, 'index']);
            Route::get('/ajax', [PpcLogBookController::class, 'ajax']);
            Route::get('/edit', [PpcLogBookController::class, 'edit']);
            Route::post('/update', [PpcLogBookController::class, 'update']);
        });
    });
});
