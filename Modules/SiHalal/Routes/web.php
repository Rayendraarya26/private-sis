<?php

use Illuminate\Support\Facades\Route;
use Modules\SiHalal\Http\Controllers\SiHalalController;
use Modules\SiHalal\Http\Controllers\ManagePermohonanController;
use Modules\SiHalal\Http\Controllers\ManageBiayaController;
use Modules\SiHalal\Http\Controllers\ManageAuditController;
use Modules\SiHalal\Http\Controllers\DataAuditorController;

Route::prefix('sihalal')->middleware(['auth', 'restrict'])->group(function () {
    Route::get('/', [SiHalalController::class, 'index']);
	
	Route::prefix("permohonan")->group(function () {
        Route::get("/", [ManagePermohonanController::class, 'index']);
        Route::any("/ajax", [ManagePermohonanController::class, 'ajax']);
		Route::get('/detail/{reg_id}', [ManagePermohonanController::class, 'detail']);
        Route::post('/update', [ManagePermohonanController::class, 'update']);
    });
	
	Route::prefix("biaya")->group(function () {
        Route::get("/", [ManageBiayaController::class, 'index']);
		Route::any("/ajax", [ManageBiayaController::class, 'ajax']);
		Route::get('/detail/{reg_id}', [ManageBiayaController::class, 'detail']);
        Route::post('/addBiaya', [ManageBiayaController::class, 'addBiaya']);
        Route::post('/updateBiaya', [ManageBiayaController::class, 'updateBiaya']);
        Route::post('/deleteBiaya', [ManageBiayaController::class, 'deleteBiaya']);
        Route::post('/updateStatus', [ManageBiayaController::class, 'updateStatus']);
    });
	
	Route::prefix("audit")->group(function () {
        Route::get("/", [ManageAuditController::class, 'index']);
    });
	
	Route::prefix("ref-auditor")->group(function () {
        Route::get("/", [DataAuditorController::class, 'index']);
    });
});
