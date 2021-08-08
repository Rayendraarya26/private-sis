<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\ForgetPasswordController;
use Modules\Auth\Http\Controllers\LoginController;
use Modules\Auth\Http\Controllers\RegisterController;

Route::prefix('auth')->name("auth.")->group(function () {
    Route::middleware('guest')->group(function () {
        Route::redirect("/", "auth/login");
        Route::get('login', [LoginController::class, 'index'])->name('login');
        Route::post('login', [LoginController::class, 'handleLogin'])->name("processLogin");
        Route::get('oauth/google', [LoginController::class, 'redirectToGoogle'])->name('google');
        Route::get('oauth/google/callback', [LoginController::class, 'handleGoogleCallback']);

        Route::get('register', [RegisterController::class, 'index'])->name('register');
        Route::post('register', [RegisterController::class, 'handleRegister'])->name('processRegister');

        Route::get('forget-password', [ForgetPasswordController::class, 'index'])->name('forget_password');
        Route::post('forget-password', [ForgetPasswordController::class, 'handleResetPassword']);
        Route::get('new-password/{token}', [ForgetPasswordController::class, 'resetPassword'])->name('reset_password');
        Route::post('new-password', [ForgetPasswordController::class, 'handleNewPassword'])->name('new_password');
    });

    Route::post('switch-role', [LoginController::class, 'switchRole'])->name('switch_role');
    Route::get('validation/resend', [LoginController::class, 'resendValidation'])->name('resend_validation');
    Route::post('validation/resend', [LoginController::class, 'handleResendValidation']);
    Route::get('validation/verify/{token}', [LoginController::class, 'verifyValidation'])->name('verify');

    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
});
