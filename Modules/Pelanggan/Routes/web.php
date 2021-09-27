<?php

use Illuminate\Support\Facades\Route;
use Modules\Pelanggan\Http\Controllers\PelangganController;

Route::prefix('pelanggan')->group(function () {
    Route::get('/', [PelangganController::class, 'index']);
});
