<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Http\Controllers\BillingController;

Route::prefix('keuangan')->group(function() {
    Route::prefix("billing")->group(function () {
        Route::get('/', [BillingController::class, 'index']);
        Route::get('/detail', [BillingController::class, 'detail']);
        Route::get('/ajax', [BillingController::class, 'ajax']);
        Route::get('/create', [BillingController::class, 'create']);
        Route::post('/store', [BillingController::class, 'store']);
        Route::get('/edit', [BillingController::class, 'edit']);
        Route::post('/update', [BillingController::class, 'update']);
        Route::delete('/delete', [BillingController::class, 'destroy']);
    });
});
