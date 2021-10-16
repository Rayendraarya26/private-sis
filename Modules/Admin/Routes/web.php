<?php


use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\PegawaiController;
use Modules\Admin\Http\Controllers\PelangganController;

Route::prefix('admin')->group(function () {
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
});
