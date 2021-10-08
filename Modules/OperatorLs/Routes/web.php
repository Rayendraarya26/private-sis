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
use Modules\OperatorLs\Http\Controllers\OperatorLsController;
use Modules\OperatorLs\Http\Controllers\KelengkapanPermohonanController;
use Modules\OperatorLs\Http\Controllers\BillingController;
use Modules\OperatorLs\Http\Controllers\PenjadwalanController;

Route::prefix('operatorls')->group(function () {
    Route::get('/', [OperatorLsController::class, 'index']);
	
	Route::prefix("kelengkapan-permohonan")->group(function () {
        Route::get('/', [KelengkapanPermohonanController::class, 'index']);
        Route::get('/ajax', [KelengkapanPermohonanController::class, 'ajax']);
        Route::get('/detail/{mohon_id}', [KelengkapanPermohonanController::class, 'detail']);
        Route::get('/edit', [KelengkapanPermohonanController::class, 'edit']);
        Route::post('/update', [KelengkapanPermohonanController::class, 'update']);
    });
	
	
	Route::prefix("billing")->group(function () {
        Route::get('/', [BillingController::class, 'index']);
        Route::get('/ajax', [BillingController::class, 'ajax']);
    });
	
	Route::prefix("penjadwalan")->group(function () {
        Route::get('/', [PenjadwalanController::class, 'index']);
        Route::get('/ajax', [PenjadwalanController::class, 'ajax']);
    });
});
