<?php

use Illuminate\Support\Facades\Route;
use Modules\KerjaSama\Http\Controllers\SPKController;

Route::prefix('kerjasama')->middleware(['auth', 'restrict'])->group(function () {
    Route::get("/spk", [SPKController::class, 'index']);
    Route::any("/spk/ajax", [SPKController::class, 'ajax']);
});
