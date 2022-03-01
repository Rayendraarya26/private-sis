<?php

use Illuminate\Support\Facades\Route;
use Modules\SiHalal\Http\Controllers\SiHalalController;

Route::prefix('sihalal')->middleware(['auth', 'restrict'])->group(function () {
    Route::get('/', [SiHalalController::class, 'index']);
});
