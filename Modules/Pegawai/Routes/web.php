<?php

use Illuminate\Support\Facades\Route;
use Modules\Pegawai\Http\Controllers\PegawaiController;

Route::prefix('pegawai')->middleware(['auth', 'restrict'])->group(function () {
    Route::get('/', [PegawaiController::class, 'index']);
});
