<?php

use Illuminate\Support\Facades\Route;
use Modules\Pjt\Http\Controllers\VerifKajianPermohonanController;

Route::prefix('pjt')->middleware(['auth', 'restrict'])->group(function () {
    Route::get("/verifikasi", [VerifKajianPermohonanController::class, 'index']);
    Route::any("/verifikasi/ajax", [VerifKajianPermohonanController::class, 'ajax']);
});
