<?php

use Illuminate\Support\Facades\Route;
use Modules\Master\Http\Controllers\BadanHukumController;

Route::prefix('master')->group(function () {
    Route::prefix("badan-hukum")->group(function () {
        Route::get('/', [BadanHukumController::class, 'index']);
        Route::get('/ajax', [BadanHukumController::class, 'ajax']);
        Route::get('/create', [BadanHukumController::class, 'create']);
        Route::post('/store', [BadanHukumController::class, 'store']);
        Route::get('/edit/{badanHukumId}', [BadanHukumController::class, 'edit']);
        Route::post('/update', [BadanHukumController::class, 'update']);
        Route::delete('/delete/{badanHukumId}', [BadanHukumController::class, 'destroy']);
    });
});
