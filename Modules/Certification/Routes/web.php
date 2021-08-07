<?php

use Illuminate\Support\Facades\Route;
use Modules\Certification\Http\Controllers\RequestCertificateController;
use Modules\Certification\Http\Controllers\VerifCertificateController;

Route::prefix('certification')->middleware(['auth', 'restrict'])->group(function () {
    Route::get('/request-certificate', [RequestCertificateController::class, 'index']);

    Route::get('/verif-certificate', [VerifCertificateController::class, 'index']);
});
