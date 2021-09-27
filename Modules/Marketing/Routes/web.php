<?php


use Illuminate\Support\Facades\Route;
use Modules\Marketing\Http\Controllers\MarketingController;

Route::prefix('marketing')->group(function() {
    Route::get('/', [MarketingController::class, 'index']);
});
