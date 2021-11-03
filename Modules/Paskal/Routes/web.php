<?php


use Illuminate\Support\Facades\Route;
use Modules\Paskal\Http\Controllers\VerifKajianPermohonanController;

Route::prefix('paskal')->middleware(['auth', 'restrict'])->group(function () {
    Route::get("/verifikasi", [VerifKajianPermohonanController::class, 'index']);
    Route::any("/verifikasi/ajax", [VerifKajianPermohonanController::class, 'ajax']);
});
