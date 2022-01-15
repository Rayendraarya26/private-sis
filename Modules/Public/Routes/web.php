<?php

use Illuminate\Support\Facades\Route;
use Modules\Public\Http\Controllers\PublicController;
use Modules\Public\Http\Controllers\TrackController;

Route::get('/', [PublicController::class, 'index']);
Route::get('/track/certification/{key}', [TrackController::class, 'certification']);
Route::get('/track/certificate/{key?}', [TrackController::class, 'certificate']);
Route::post('/track/certificate', [TrackController::class, 'doTrackCertificate']);
