<?php

use Illuminate\Support\Facades\Route;
use Modules\KoordinatorSertifikasi\Http\Controllers\VerifLapLengkapController;

Route::prefix('koordinatorsertifikasi')->middleware(['auth', 'restrict'])->group(function () {
    Route::prefix('verif')->group(function(){
		Route::get('/', [VerifLapLengkapController::class, 'index']);
		Route::get('/ajax', [VerifLapLengkapController::class, 'ajax']);
		Route::get("/cetak/{jadw_id}/{type}", [VerifLapLengkapController::class, 'cetak'])->where('type', 'lap-lengkap');
		Route::get('/detail/{jadw_id}', [VerifLapLengkapController::class, 'detail']);
		Route::post('/verifikasi/{jadw_id}', [VerifLapLengkapController::class, 'verifikasi']);
    });
});
