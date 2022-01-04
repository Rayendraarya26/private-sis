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
use Modules\TimAudit\Http\Controllers\AuLapObservasiController;
use Modules\TimAudit\Http\Controllers\AuLksController;
use Modules\TimAudit\Http\Controllers\AuLogBookController;
use Modules\TimAudit\Http\Controllers\AuPengajuanKomiteController;
use Modules\TimAudit\Http\Controllers\AuTahap1Controller;
use Modules\TimAudit\Http\Controllers\AuUploadJadwalController;
use Modules\TimAudit\Http\Controllers\AuUploadJadwalTahap1Controller;
use Modules\TimAudit\Http\Controllers\KomiteBeritaAcaraController;
use Modules\TimAudit\Http\Controllers\KomiteDaftarHadirController;
use Modules\TimAudit\Http\Controllers\KomiteLembarPeriksaController;
use Modules\TimAudit\Http\Controllers\PersetujuanTimAuditController;
use Modules\TimAudit\Http\Controllers\PpcLaporanController;
use Modules\TimAudit\Http\Controllers\PpcLogBookController;

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
            Route::get('/temuan/{jadw_id}/verifikasi', [AuLksController::class, 'verifikasiTemuan']);
            Route::post('/temuan/{jadw_id}/verifikasi', [AuLksController::class, 'processVerifikasiTemuan']);
            Route::post('/temuan/{jadw_id}/revisi', [AuLksController::class, 'processRevisiTemuan']);
            Route::post('/temuan/{jadw_id}/generate', [AuLksController::class, 'generate']);
            Route::post('/temuan/{jadw_id}/save-draft', [AuLksController::class, 'saveDraft']);
            Route::post('/temuan/{jadw_id}/delete/{lks_id}', [AuLksController::class, 'deleteTemuan']);

            Route::get('/temuan/{jadw_id}/tambah', [AuLksController::class, 'addTemuan']);
            Route::post('/temuan/{jadw_id}/tambah', [AuLksController::class, 'storeTemuan']);
            Route::get('/temuan/{jadw_id}/edit/{lks_id}', [AuLksController::class, 'editTemuan']);
            Route::get('/temuan/{jadw_id}/detail/{lks_id}', [AuLksController::class, 'detailTemuan']);
            Route::post('/temuan/{jadw_id}/verif/{lks_id}', [AuLksController::class, 'verifTemuan']);
        });

        Route::prefix("laporan-ringkas")->group(function () {
            Route::get('/', [AuLapRingkasController::class, 'index']);
            Route::get('/ajax', [AuLapRingkasController::class, 'ajax']);
            Route::get('/laporan/{jadw_id}', [AuLapRingkasController::class, 'laporan']);
            Route::post('/laporan/{jadw_id}', [AuLapRingkasController::class, 'processLaporan']);
        });

		Route::prefix("laporan-observasi")->group(function () {
            Route::get('/', [AuLapObservasiController::class, 'index']);
            Route::get('/ajax', [AuLapObservasiController::class, 'ajax']);
            Route::get('/laporan/{jadw_id}', [AuLapObservasiController::class, 'laporan']);
            Route::post('/laporan/{jadw_id}', [AuLapObservasiController::class, 'processLaporan']);
        });

        Route::prefix("laporan-lengkap")->group(function () {
            Route::get('/', [AuLapLengkapController::class, 'index']);
            Route::get('/ajax', [AuLapLengkapController::class, 'ajax']);
            Route::get("/preview/{jadw_id}/{type}", [AuLapLengkapController::class, 'preview'])->where('type', 'lap-lengkap');
            Route::get('/laporan/{jadw_id}', [AuLapLengkapController::class, 'laporan']);
            Route::post('/laporan/{jadw_id}', [AuLapLengkapController::class, 'processLaporan']);
        });

        Route::prefix("daftar-hadir")->group(function () {
            Route::get('/', [AuDaftarHadirController::class, 'index']);
            Route::get('/ajax', [AuDaftarHadirController::class, 'ajax']);
            Route::get('/unggah/{jadw_id}', [AuDaftarHadirController::class, 'unggah']);
            Route::post('/unggah/{jadw_id}', [AuDaftarHadirController::class, 'storeUnggah']);
            Route::get("/detail/{jadw_id}/{type}", [AuDaftarHadirController::class, 'detail'])->where('type', 'lap-ringkas|lks');
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
            Route::get('/edit', [AuPengajuanKomiteController::class, 'edit']);
            Route::post('/update', [AuPengajuanKomiteController::class, 'update']);			
            Route::get("/detail/{jadw_id}/{type}", [AuPengajuanKomiteController::class, 'detail'])->where('type', 'lap-lengkap|lap-ringkas|lks|detail-audit');
        });
    });


    // ============================== Komite ==============================
    Route::prefix("komite")->group(function () {
        Route::prefix("daftar-hadir")->group(function () {
            Route::get('/', [KomiteDaftarHadirController::class, 'index']);
            Route::any('/ajax', [KomiteDaftarHadirController::class, 'ajax']);
            Route::get('/edit', [KomiteDaftarHadirController::class, 'edit']);
            Route::post('/update', [KomiteDaftarHadirController::class, 'update']);
        });

        Route::prefix("lembar-periksa")->group(function () {
            Route::get('/', [KomiteLembarPeriksaController::class, 'index']);
            Route::any('/ajax', [KomiteLembarPeriksaController::class, 'ajax']);
            Route::get('/edit', [KomiteLembarPeriksaController::class, 'edit']);
            Route::post('/update', [KomiteLembarPeriksaController::class, 'update']);
        });

        Route::prefix("berita-acara")->group(function () {
            Route::get('/', [KomiteBeritaAcaraController::class, 'index']);
            Route::any('/ajax', [KomiteBeritaAcaraController::class, 'ajax']);
            Route::get('/detail', [KomiteBeritaAcaraController::class, 'detail']);
            Route::get('/edit', [KomiteBeritaAcaraController::class, 'edit']);
            Route::post('/update', [KomiteBeritaAcaraController::class, 'update']);
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
