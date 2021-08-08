<?php

use Illuminate\Support\Facades\Route;
use Modules\Home\Http\Controllers\DashboardController;
use Modules\Home\Http\Controllers\NotificationController;


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('notification')->group(function () {
        Route::get("/", [NotificationController::class, 'index'])->name('notification');
        Route::get("open/{id}", [NotificationController::class, 'open']);
        Route::get("mark-all-as-read", [NotificationController::class, 'markAllAsRead']);
        Route::get("/tes", [NotificationController::class, 'tes']);
        Route::post("/ajax/sync-token", [NotificationController::class, 'ajaxSyncToken']);
    });
});
