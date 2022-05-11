<?php


use Illuminate\Support\Facades\Route;

Route::prefix('archive')->group(function() {
    Route::get('/', 'ArchiveController@index');
});
