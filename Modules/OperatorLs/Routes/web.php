<?php

use Illuminate\Support\Facades\Route;
use Modules\OperatorLs\Http\Controllers\JadwalSurveilantController;
use Modules\OperatorLs\Http\Controllers\KelengkapanPermohonanController;
use Modules\OperatorLs\Http\Controllers\KomiteController;
use Modules\OperatorLs\Http\Controllers\PenjadwalanController;
use Modules\OperatorLs\Http\Controllers\SertifikatUjiController;
use Modules\OperatorLs\Http\Controllers\TimController;
use Modules\OperatorLs\Http\Controllers\PenjadwalanUlangTimController;
use Modules\OperatorLs\Http\Controllers\PenjadwalanTahap1Controller;
use Modules\OperatorLs\Http\Controllers\UploadKajianPermohonanController;
use Modules\OperatorLs\Http\Controllers\PenjadwalanPencabutanController;
use Modules\OperatorLs\Http\Controllers\RekomPersetujuanController;
use Modules\OperatorLs\Http\Controllers\DataSertifikatController;

Route::prefix('operatorls')->middleware(['auth', 'restrict'])->group(function () {

	Route::prefix("data-sertifikat")->group(function () {
        Route::get("/", [DataSertifikatController::class, 'index']);
        Route::get("/ajax", [DataSertifikatController::class, 'ajax']);
        Route::get('/cetak/{sertifikat_id}', [DataSertifikatController::class, 'cetak']);
        Route::get('/upload/{sertifikat_id}', [DataSertifikatController::class, 'uploadSertifikat']);
        Route::post('/save', [DataSertifikatController::class, 'saveSertifikat']);
    });

	Route::prefix("jadwal-surveilant")->group(function () {
        Route::get("/", [JadwalSurveilantController::class, 'index']);
        Route::get("/ajax", [JadwalSurveilantController::class, 'ajax']);
        Route::post('/reminder/finance', [JadwalSurveilantController::class, 'reminderFinance']);
    });

	Route::prefix("rekomendasi-persetujuan")->group(function () {
            Route::get('/', [RekomPersetujuanController::class, 'index']);
            Route::any('/ajax', [RekomPersetujuanController::class, 'ajax']);
            Route::get('/edit', [RekomPersetujuanController::class, 'edit']);
            Route::post('/update', [RekomPersetujuanController::class, 'update']);
        });

	Route::prefix("kajian-permohonan")->group(function () {
        Route::get('/', [UploadKajianPermohonanController::class, 'index']);
        Route::any('/ajax', [UploadKajianPermohonanController::class, 'ajax']);
        Route::get('/detail/{mohon_id}', [UploadKajianPermohonanController::class, 'detail']);
        Route::get('/edit', [UploadKajianPermohonanController::class, 'edit']);
        Route::post('/update', [UploadKajianPermohonanController::class, 'update']);
    });

	Route::prefix("kelengkapan-permohonan")->group(function () {
        Route::get('/', [KelengkapanPermohonanController::class, 'index']);
        Route::get('/ajax', [KelengkapanPermohonanController::class, 'ajax']);
        Route::get('/detail/{mohon_id}', [KelengkapanPermohonanController::class, 'detail']);
        Route::get('/edit', [KelengkapanPermohonanController::class, 'edit']);
        Route::post('/update', [KelengkapanPermohonanController::class, 'update']);
    });

	Route::prefix("penjadwalan")->group(function () {
        Route::get('/', [PenjadwalanController::class, 'index']);
        Route::get('/detail', [PenjadwalanController::class, 'detail']);
        Route::get('/ajax', [PenjadwalanController::class, 'ajax']);
        Route::get('/create', [PenjadwalanController::class, 'create']);
        Route::post('/store', [PenjadwalanController::class, 'store']);
        Route::get('/edit', [PenjadwalanController::class, 'edit']);
        Route::post('/update', [PenjadwalanController::class, 'update']);
        Route::delete('/delete', [PenjadwalanController::class, 'destroy']);
    });

	Route::prefix("penjadwalan-tahap1")->group(function () {
        Route::get('/', [PenjadwalanTahap1Controller::class, 'index']);
        Route::get('/detail', [PenjadwalanTahap1Controller::class, 'detail']);
        Route::get('/ajax', [PenjadwalanTahap1Controller::class, 'ajax']);
        Route::get('/create', [PenjadwalanTahap1Controller::class, 'create']);
        Route::post('/store', [PenjadwalanTahap1Controller::class, 'store']);
        Route::get('/edit', [PenjadwalanTahap1Controller::class, 'edit']);
        Route::post('/update', [PenjadwalanTahap1Controller::class, 'update']);
        Route::delete('/delete', [PenjadwalanTahap1Controller::class, 'destroy']);
    });


	Route::prefix("tim")->group(function () {
        Route::get('/', [TimController::class, 'index']);
        Route::get('/detail', [TimController::class, 'detail']);
        Route::get('/ajax', [TimController::class, 'ajax']);
        Route::get('/create', [TimController::class, 'create']);
        Route::post('/store', [TimController::class, 'store']);
        Route::get('/edit', [TimController::class, 'edit']);
        Route::post('/update', [TimController::class, 'update']);
        Route::delete('/delete', [TimController::class, 'destroy']);
    });

	Route::prefix("penjadwalan-ulang-tim")->group(function () {
        Route::get('/', [PenjadwalanUlangTimController::class, 'index']);
        Route::get('/detail', [PenjadwalanUlangTimController::class, 'detail']);
        Route::get('/ajax', [PenjadwalanUlangTimController::class, 'ajax']);
        Route::get('/edit', [PenjadwalanUlangTimController::class, 'edit']);
        Route::post('/update', [PenjadwalanUlangTimController::class, 'update']);
        Route::delete('/delete', [PenjadwalanUlangTimController::class, 'destroy']);
    });

	Route::prefix("komite")->group(function () {
        Route::get('/', [KomiteController::class, 'index']);
        Route::get('/detail', [KomiteController::class, 'detail']);
        Route::get('/ajax', [KomiteController::class, 'ajax']);
        Route::get('/create', [KomiteController::class, 'create']);
        Route::post('/store', [KomiteController::class, 'store']);
        Route::get('/edit', [KomiteController::class, 'edit']);
        Route::post('/update', [KomiteController::class, 'update']);
        Route::delete('/delete', [KomiteController::class, 'destroy']);
    });

	Route::prefix("sertifikat-uji")->group(function () {
        Route::get('/', [SertifikatUjiController::class, 'index']);
        Route::get('/detail', [SertifikatUjiController::class, 'detail']);
        Route::get('/ajax', [SertifikatUjiController::class, 'ajax']);
        Route::get('/edit', [SertifikatUjiController::class, 'edit']);
        Route::post('/update', [SertifikatUjiController::class, 'update']);
    });

	Route::prefix("jadwal-pencabutan")->group(function () {
        Route::get('/', [PenjadwalanPencabutanController::class, 'index']);
        Route::get('/detail', [PenjadwalanPencabutanController::class, 'detail']);
        Route::get('/ajax', [PenjadwalanPencabutanController::class, 'ajax']);
        Route::get('/create', [PenjadwalanPencabutanController::class, 'create']);
        Route::post('/store', [PenjadwalanPencabutanController::class, 'store']);
        Route::get('/edit', [PenjadwalanPencabutanController::class, 'edit']);
        Route::post('/update', [PenjadwalanPencabutanController::class, 'update']);
        Route::delete('/delete', [PenjadwalanPencabutanController::class, 'destroy']);
    });

});
