<?php

use Illuminate\Support\Facades\Route;
use Modules\SiHalal\Http\Controllers\SiHalalController;
use Modules\SiHalal\Http\Controllers\ManagePermohonanController;
use Modules\SiHalal\Http\Controllers\ManageInvoiceController;
use Modules\SiHalal\Http\Controllers\ManageAuditController;
use Modules\SiHalal\Http\Controllers\DataAuditorController;

Route::prefix('sihalal')->middleware(['auth', 'restrict'])->group(function () {
    Route::get('/', [SiHalalController::class, 'index']);
	
	Route::prefix("permohonan")->group(function () {
        Route::get("/", [ManagePermohonanController::class, 'index']);
    });
	
	Route::prefix("invoice")->group(function () {
        Route::get("/", [ManageInvoiceController::class, 'index']);
    });
	
	Route::prefix("audit")->group(function () {
        Route::get("/", [ManageAuditController::class, 'index']);
    });
	
	Route::prefix("ref-auditor")->group(function () {
        Route::get("/", [DataAuditorController::class, 'index']);
    });
});
