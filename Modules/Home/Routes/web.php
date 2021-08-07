<?php

use Illuminate\Support\Facades\Route;
use Modules\Home\Http\Controllers\DashboardController;


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
