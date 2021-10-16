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
        Route::get('ajax', [AccountController::class, 'ajax'])->name('ajax');;
        Route::get('profile', [AccountController::class, 'index'])->name('profile');
        Route::get('update/profile', [AccountController::class, 'editProfile'])->name('update_profile');
        Route::post('update/profile', [AccountController::class, 'updateProfile']);
        Route::get('update/password', [AccountController::class, 'editPassword'])->name('change_password');
        Route::post('update/password', [AccountController::class, 'updatePassword']);
    });
});
