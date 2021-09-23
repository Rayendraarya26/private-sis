<?php

use Illuminate\Support\Facades\Route;
use Modules\Master\Http\Controllers\BadanHukumController;
use Modules\Master\Http\Controllers\KodePosController;
use Modules\Master\Http\Controllers\NegaraController;
use Modules\Master\Http\Controllers\ProvinsiController;
use Modules\Master\Http\Controllers\KabupatenController;
use Modules\Master\Http\Controllers\KecamatanController;
use Modules\Master\Http\Controllers\JenisDokPerusahaanController;
use Modules\Master\Http\Controllers\JenisPerusahaanController;

Route::prefix('master')->group(function () {

    Route::prefix("provinsi")->group(function () {
        Route::get('/', [ProvinsiController::class, 'index']);
        Route::get('/ajax', [ProvinsiController::class, 'ajax']);
        Route::get('/create', [ProvinsiController::class, 'create']);
        Route::post('/store', [ProvinsiController::class, 'store']);
        Route::get('/edit/{provId}', [ProvinsiController::class, 'edit']);
        Route::post('/update', [ProvinsiController::class, 'update']);
        Route::delete('/delete/{provId}', [ProvinsiController::class, 'destroy']);
    });

    Route::prefix("kabupaten")->group(function () {
        Route::get('/', [KabupatenController::class, 'index']);
        Route::get('/ajax', [KabupatenController::class, 'ajax']);
        Route::get('/create', [KabupatenController::class, 'create']);
        Route::post('/store', [KabupatenController::class, 'store']);
        Route::get('/edit/{kabId}', [KabupatenController::class, 'edit']);
        Route::post('/update', [KabupatenController::class, 'update']);
        Route::delete('/delete/{kabId}', [KabupatenController::class, 'destroy']);
    });

    Route::prefix("kecamatan")->group(function () {
        Route::get('/', [KecamatanController::class, 'index']);
        Route::get('/ajax', [KecamatanController::class, 'ajax']);
        Route::get('/create', [KecamatanController::class, 'create']);
        Route::post('/store', [KecamatanController::class, 'store']);
        Route::get('/edit/{kecId}', [KecamatanController::class, 'edit']);
        Route::post('/update', [KecamatanController::class, 'update']);
        Route::delete('/delete/{kecId}', [KecamatanController::class, 'destroy']);
    });

    Route::prefix("jenis-dok-perusahaan")->group(function () {
        Route::get('/', [JenisDokPerusahaanController::class, 'index']);
        Route::get('/ajax', [JenisDokPerusahaanController::class, 'ajax']);
        Route::get('/create', [JenisDokPerusahaanController::class, 'create']);
        Route::post('/store', [JenisDokPerusahaanController::class, 'store']);
        Route::get('/edit/{jenisDokPerusahaanId}', [JenisDokPerusahaanController::class, 'edit']);
        Route::post('/update', [JenisDokPerusahaanController::class, 'update']);
        Route::delete('/delete/{jenisDokPerusahaanId}', [JenisDokPerusahaanController::class, 'destroy']);
    });

    Route::prefix("badan-hukum")->group(function () {
        Route::get('/', [BadanHukumController::class, 'index']);
        Route::get('/ajax', [BadanHukumController::class, 'ajax']);
        Route::get('/create', [BadanHukumController::class, 'create']);
        Route::post('/store', [BadanHukumController::class, 'store']);
        Route::get('/edit/{badanHukumId}', [BadanHukumController::class, 'edit']);
        Route::post('/update', [BadanHukumController::class, 'update']);
        Route::delete('/delete/{badanHukumId}', [BadanHukumController::class, 'destroy']);
    });

    Route::prefix("jenis-dok-perusahaan")->group(function () {
        Route::get('/', [JenisDokPerusahaanController::class, 'index']);
        Route::get('/ajax', [JenisDokPerusahaanController::class, 'ajax']);
        Route::get('/create', [JenisDokPerusahaanController::class, 'create']);
        Route::post('/store', [JenisDokPerusahaanController::class, 'store']);
        Route::get('/edit/{jenisDokPerusahaanId}', [JenisDokPerusahaanController::class, 'edit']);
        Route::post('/update', [JenisDokPerusahaanController::class, 'update']);
        Route::delete('/delete/{jenisDokPerusahaanId}', [JenisDokPerusahaanController::class, 'destroy']);
    });

    Route::prefix("negara")->group(function () {
        Route::get('/', [NegaraController::class, 'index']);
        Route::get('/ajax', [NegaraController::class, 'ajax']);
        Route::get('/create', [NegaraController::class, 'create']);
        Route::post('/store', [NegaraController::class, 'store']);
        Route::get('/edit/{negaraId}', [NegaraController::class, 'edit']);
        Route::post('/update', [NegaraController::class, 'update']);
        Route::delete('/delete/{negaraId}', [NegaraController::class, 'destroy']);
    });

    Route::prefix("kode-pos")->group(function () {
        Route::get('/', [KodePosController::class, 'index']);
        Route::get('/ajax', [KodePosController::class, 'ajax']);
        Route::get('/create', [KodePosController::class, 'create']);
        Route::post('/store', [KodePosController::class, 'store']);
        Route::get('/edit/{kodePosId}', [KodePosController::class, 'edit']);
        Route::post('/update', [KodePosController::class, 'update']);
        Route::delete('/delete/{kodePosId}', [KodePosController::class, 'destroy']);
    });

    Route::prefix("jenis-perusahaan")->group(function () {
        Route::get('/', [JenisPerusahaanController::class, 'index']);
        Route::get('/ajax', [JenisPerusahaanController::class, 'ajax']);
        Route::get('/create', [JenisPerusahaanController::class, 'create']);
        Route::post('/store', [JenisPerusahaanController::class, 'store']);
        Route::get('/edit/{jenisPerusahaanId}', [JenisPerusahaanController::class, 'edit']);
        Route::post('/update', [JenisPerusahaanController::class, 'update']);
        Route::delete('/delete/{jenisPerusahaanId}', [JenisPerusahaanController::class, 'destroy']);
    });
});
