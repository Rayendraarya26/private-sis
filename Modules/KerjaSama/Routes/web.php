<?php

use Illuminate\Support\Facades\Route;
use Modules\KerjaSama\Http\Controllers\SPKController;

Route::prefix('kerjasama')->middleware(['auth', 'restrict'])->group(function () {
	 Route::prefix("spk")->group(function () {
        Route::get('/', [SPKController::class, 'index']);
        Route::get('/detail', [SPKController::class, 'detail']);
        Route::get('/ajax', [SPKController::class, 'ajax']);
        Route::get('/edit', [SPKController::class, 'edit']);
        Route::post('/update', [SPKController::class, 'update']);
    });
});
