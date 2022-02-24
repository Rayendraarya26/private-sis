<?php

use Illuminate\Support\Facades\Route;
use Modules\KoordinatorSertifikasi\Http\Controllers\VerifLapTahap1Controller;
use Modules\KoordinatorSertifikasi\Http\Controllers\VerifLapLengkapController;

Route::prefix('koordinatorsertifikasi')->middleware(['auth', 'restrict'])->group(function () {
    Route::prefix('verif')->group(function(){
		Route::get('/', [VerifLapLengkapController::class, 'index']);
		Route::get('/ajax', [VerifLapLengkapController::class, 'ajax']);
		Route::get("/cetak/{jadw_id}/{type}", [VerifLapLengkapController::class, 'cetak'])->where('type', 'lap-lengkap');
		Route::get('/detail/{jadw_id}', [VerifLapLengkapController::class, 'detail']);
		Route::post('/verifikasi', [VerifLapLengkapController::class, 'verifikasi']);
    });
	
	 Route::prefix('verif-lap-tahap1')->group(function(){
		Route::get('/', [VerifLapTahap1Controller::class, 'index']);
		Route::get('/ajax', [VerifLapTahap1Controller::class, 'ajax']);
		Route::get("/cetak", [VerifLapTahap1Controller::class, 'cetak']);
		Route::get('/verif', [VerifLapTahap1Controller::class, 'verif']);
		Route::post('/doVerif', [VerifLapTahap1Controller::class, 'doVerif']);
    });
});
