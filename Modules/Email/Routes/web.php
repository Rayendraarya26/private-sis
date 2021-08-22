<?php

use Illuminate\Support\Facades\Route;
use Modules\Email\Http\Controllers\EmailController;
use Modules\Email\Http\Controllers\HistoryEmailSchedulerController;
use Modules\Email\Http\Controllers\HistoryEmailSystemController;
use Modules\Email\Http\Controllers\SchedulerController;
use Modules\Email\Http\Controllers\TemplateEmailController;

Route::prefix('email')->middleware(['auth', 'restrict'])->group(function () {
    Route::get('template', [TemplateEmailController::class, 'index']);
    Route::post('template', [TemplateEmailController::class, 'store']);
    Route::get('template/create', [TemplateEmailController::class, 'create']);
    Route::get('template/edit/{uuid}', [TemplateEmailController::class, 'edit']);
    Route::post('template/update', [TemplateEmailController::class, 'update']);
    Route::delete("template/delete/{uuid}", [TemplateEmailController::class, 'destroy']);
    Route::any("template/ajax", [TemplateEmailController::class, 'ajax']);
    Route::get("template/preview", [TemplateEmailController::class, 'previewEmail']);

    Route::get('history/system', [HistoryEmailSystemController::class, 'index']);
    Route::get('history/system/preview', [HistoryEmailSystemController::class, 'previewEmail']);
    Route::get('history/system/ajax', [HistoryEmailSystemController::class, 'ajax']);

    Route::get('history/scheduler', [HistoryEmailSchedulerController::class, 'index']);
    Route::get('history/scheduler/preview', [HistoryEmailSchedulerController::class, 'previewEmail']);
    Route::get('history/scheduler/ajax', [HistoryEmailSchedulerController::class, 'ajax']);
});

Route::prefix('email')->group(function () {
    Route::get("open/{uuid}", [EmailController::class, 'open']);
    Route::get("schedule/send-greeting", [SchedulerController::class, 'sendGreeting']);
});
