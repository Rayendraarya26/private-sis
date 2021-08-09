<?php

use Illuminate\Support\Facades\Route;
use Modules\System\Http\Controllers\ManageGroupController;
use Modules\System\Http\Controllers\ManageMenuActionController;
use Modules\System\Http\Controllers\ManageMenuController;
use Modules\System\Http\Controllers\ManageUserController;

Route::prefix('system')->middleware(['auth', 'restrict'])->name("system")->group(function () {
    Route::redirect("/", "/system/menu");

    Route::resource("user", ManageUserController::class);
    Route::get('user/ajax/datagrid', [ManageUserController::class, 'ajaxDatagrid']);
    Route::post('user/ajax/banned', [ManageUserController::class, 'ajaxBanned']);

    Route::resource("group", ManageGroupController::class);
    Route::get('group/ajax/datagrid', [ManageGroupController::class, 'ajaxDatagrid']);
    Route::get('group/ajax/treegrid', [ManageGroupController::class, 'ajaxTreegrid']);
    Route::post('group/ajax/active', [ManageGroupController::class, 'ajaxDeactive']);

    Route::resource("menu", ManageMenuController::class);
    Route::get('menu/ajax/data-icon', [ManageMenuController::class, 'ajaxDataIcon']);
    Route::get('menu/ajax/treegrid', [ManageMenuController::class, 'ajaxTreegrid']);
    Route::post('menu/ajax/active', [ManageMenuController::class, 'ajaxDeactive']);

    Route::resource("menu/{id}/menu-action", ManageMenuActionController::class);
    Route::post('menu/{id}/menu-action/update', [ManageMenuActionController::class, 'update']);
    Route::get('menu/{id}/menu-action/ajax/datagrid', [ManageMenuActionController::class, 'ajaxDatagrid']);
    Route::post('menu/{id}/menu-action/ajax/active', [ManageMenuActionController::class, 'ajaxDeactive']);
});
