<?php

use Illuminate\Support\Facades\Route;
use Modules\Home\Http\Controllers\AccountController;
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

    Route::prefix('account')->group(function () {
        Route::redirect("/", "account/profile");
        Route::get('profile', [AccountController::class, 'index'])->name('profile');
        Route::get('change-password', [AccountController::class, 'editPassword'])->name('change_password');
        Route::post('change-password', [AccountController::class, 'updatePassword']);
    });
});
