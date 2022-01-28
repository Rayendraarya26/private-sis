<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\HomepageController;
use Modules\Admin\Http\Controllers\KompetensiAuditorController;
use Modules\Admin\Http\Controllers\KompetensiKomiteController;
use Modules\Admin\Http\Controllers\KompetensiPpcController;
use Modules\Admin\Http\Controllers\PegawaiController;
use Modules\Admin\Http\Controllers\PelangganController;

Route::prefix('admin')->middleware(['auth', 'restrict'])->group(function () {
    Route::prefix("data/pelanggan")->group(function () {
        Route::get('/', [PelangganController::class, 'index']);
        Route::get('/create', [PelangganController::class, 'create']);
        Route::post('/create', [PelangganController::class, 'store']);
        Route::get('/edit/{user_id}', [PelangganController::class, 'edit']);
        Route::put('/edit/{user_id}', [PelangganController::class, 'update']);
        Route::delete('/delete/{user_id}', [PelangganController::class, 'destroy']);
        Route::post('/banned', [PelangganController::class, 'banned']);
        Route::get("ajax", [PelangganController::class, 'ajax']);
    });

    Route::prefix("data/pegawai")->group(function () {
        Route::get('/', [PegawaiController::class, 'index']);
        Route::get('/create', [PegawaiController::class, 'create']);
        Route::post('/create', [PegawaiController::class, 'store']);
        Route::get('/edit/{user_id}', [PegawaiController::class, 'edit']);
        Route::post('/edit/{user_id}', [PegawaiController::class, 'update']);
        Route::delete('/delete/{user_id}', [PegawaiController::class, 'destroy']);
        Route::post('/banned', [PegawaiController::class, 'banned']);
        Route::get("ajax", [PegawaiController::class, 'ajax']);
    });

    Route::prefix('/kompetensi/auditor')->group(function () {

        Route::get('/', [KompetensiAuditorController::class, 'index']);
        Route::get('/ajax', [KompetensiAuditorController::class, 'ajax']);
        Route::get('/edit/by/pegawai/{pegawai_id}', [KompetensiAuditorController::class, 'editByPegawai']);
        Route::post('/saveKompetensiByPegawai', [KompetensiAuditorController::class, 'saveByPegawai']);
    });

    Route::prefix('/kompetensi/komite')->group(function () {
        Route::get('/', [KompetensiKomiteController::class, 'index']);
        Route::get('/ajax', [KompetensiKomiteController::class, 'ajax']);
        Route::get('/edit/by/pegawai/{pegawai_id}', [KompetensiKomiteController::class, 'editByPegawai']);
        Route::post('/saveKompetensiByPegawai', [KompetensiKomiteController::class, 'saveByPegawai']);
    });

    Route::prefix('/kompetensi/ppc')->group(function () {
        Route::get('/', [KompetensiPpcController::class, 'index']);
        Route::get('/ajax', [KompetensiPpcController::class, 'ajax']);
        Route::get('/edit/by/pegawai/{pegawai_id}', [KompetensiPpcController::class, 'editByPegawai']);
        Route::post('/saveKompetensiByPegawai', [KompetensiPpcController::class, 'saveByPegawai']);

    });

    Route::resource("homepage", HomepageController::class);
    Route::post('homepage/update', [HomepageController::class, 'update']);
});
