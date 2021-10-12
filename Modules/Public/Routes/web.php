<?php

use Illuminate\Support\Facades\Route;
use Modules\Public\Http\Controllers\PublicController;


Route::get('/', [PublicController::class, 'index']);
