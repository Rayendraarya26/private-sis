<?php


use Illuminate\Support\Facades\Route;
use Modules\Archive\Http\Controllers\ArchiveController;
use Modules\Archive\Http\Controllers\LogPendaftaranController;

Route::prefix('archive')->middleware(['auth', 'restrict'])->group(function () {
	
	Route::get('/', [ArchiveController::class, 'index']);
	
	Route::prefix("log_pendaftaran")->group(function () {
        Route::get("/", [LogPendaftaranController::class, 'index']);
        Route::any("/ajax", [LogPendaftaranController::class, 'ajax']);
		Route::get('/detail/{reg_id}', [LogPendaftaranController::class, 'detail']);
    });
});
