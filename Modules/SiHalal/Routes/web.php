<?php

use Illuminate\Support\Facades\Route;
use Modules\SiHalal\Http\Controllers\SiHalalController;
use Modules\SiHalal\Http\Controllers\ManagePermohonanController;
use Modules\SiHalal\Http\Controllers\ManageBiayaController;
use Modules\SiHalal\Http\Controllers\ManageAuditController;
use Modules\SiHalal\Http\Controllers\LaporanAuditController;
use Modules\SiHalal\Http\Controllers\ManageInvoiceController;
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
        Route::delete('/deleteBiaya', [ManageBiayaController::class, 'deleteBiaya']);
        Route::post('/updateStatus', [ManageBiayaController::class, 'updateStatus']);
    });
	
	Route::prefix("audit")->group(function () {
        Route::get("/", [ManageAuditController::class, 'index']);
        Route::get("/ajax", [ManageAuditController::class, 'ajax']);
        Route::get("/detail/{reg_id}", [ManageAuditController::class, 'detail']);
        Route::post("/addJadwal", [ManageAuditController::class, 'addJadwal']);
        Route::post("/updateJadwal", [ManageAuditController::class, 'updateJadwal']);
        Route::delete("/destroyJadwal", [ManageAuditController::class, 'destroyJadwal']);
        Route::post("/addAuditor", [ManageAuditController::class, 'addAuditor']);
        Route::delete("/destroyAuditor", [ManageAuditController::class, 'destroyAuditor']);
        Route::post("/updateStatus", [ManageAuditController::class, 'updateStatus']);
    });
	
	Route::prefix("laporan")->group(function () {
        Route::get("/", [LaporanAuditController::class, 'index']);
        Route::get("/ajax", [LaporanAuditController::class, 'ajax']);
        Route::get("/detail/{reg_id}", [LaporanAuditController::class, 'detail']);
        Route::post("/prosesAudit1", [LaporanAuditController::class, 'prosesAudit1']);
        Route::post("/prosesAudit2", [LaporanAuditController::class, 'prosesAudit2']);
        Route::post("/updateStatus", [LaporanAuditController::class, 'updateStatus']);
    });
	
	Route::prefix("invoice")->group(function () {
        Route::get("/", [ManageInvoiceController::class, 'index']);
        Route::get("/ajax", [ManageInvoiceController::class, 'ajax']);
        Route::get("/detail/{id}", [ManageInvoiceController::class, 'detail']);
        Route::post("/update", [ManageInvoiceController::class, 'update']);
    });
	
	Route::prefix("ref-auditor")->group(function () {
        Route::get("/", [DataAuditorController::class, 'index']);
    });
});
