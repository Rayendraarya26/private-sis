<?php

use Illuminate\Support\Facades\Route;
use Modules\Email\Http\Controllers\EmailController;
use Modules\Email\Http\Controllers\HistoryEmailReminderController;
use Modules\Email\Http\Controllers\HistoryEmailSystemController;
use Modules\Email\Http\Controllers\TemplateEmailController;

Route::prefix('email')->middleware(['auth', 'restrict'])->group(function () {
    Route::resource('template', TemplateEmailController::class);

    Route::get('history/system', [HistoryEmailSystemController::class, 'index']);
    Route::get('history/system/preview', [HistoryEmailSystemController::class, 'previewEmail']);
    Route::get('history/system/ajax', [HistoryEmailSystemController::class, 'ajax']);

    Route::get('history/reminder', [HistoryEmailReminderController::class, 'index']);
    Route::get('history/reminder/preview', [HistoryEmailReminderController::class, 'previewEmail']);
    Route::get('history/reminder/ajax', [HistoryEmailReminderController::class, 'ajax']);
});

Route::prefix('email')->group(function () {
    Route::get("open/{uuid}", [EmailController::class, 'open']);
});
