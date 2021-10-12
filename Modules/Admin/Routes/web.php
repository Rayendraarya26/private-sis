<?php


use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\PegawaiController;
use Modules\Admin\Http\Controllers\PelangganController;

Route::prefix('admin')->group(function () {
    Route::prefix("data/pelanggan")->group(function () {
        Route::get('/', [PelangganController::class, 'index']);
    });

    Route::prefix("data/pegawai")->group(function () {
        Route::get('/', [PegawaiController::class, 'index']);
    });
});
