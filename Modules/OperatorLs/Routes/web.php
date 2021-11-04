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
use Modules\OperatorLs\Http\Controllers\KelengkapanPermohonanController;
use Modules\OperatorLs\Http\Controllers\KomiteController;
use Modules\OperatorLs\Http\Controllers\OperatorLsController;
use Modules\OperatorLs\Http\Controllers\PenjadwalanController;
use Modules\OperatorLs\Http\Controllers\SertifikatUjiController;
use Modules\OperatorLs\Http\Controllers\TimController;
use Modules\OperatorLs\Http\Controllers\PenjadwalanTahap1Controller;
use Modules\OperatorLs\Http\Controllers\UploadKajianPermohonanController;

Route::prefix('operatorls')->middleware(['auth', 'restrict'])->group(function () {
    Route::get('/', [OperatorLsController::class, 'index']);
	
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
        Route::get('/ajax', [SertifikatUjiController::class, 'ajax']);
    });
});
