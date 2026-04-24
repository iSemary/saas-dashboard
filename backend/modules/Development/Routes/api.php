<?php

use Illuminate\Support\Facades\Route;
use Modules\Development\Http\Controllers\Api\DatabaseController;
use Modules\Development\Http\Controllers\Api\DevelopmentController;

// TODO Add auth middleware
Route::prefix('development')->group(function () {
    Route::get('routes', [DevelopmentController::class, "routes"]);
    Route::get('databases/flow', [DatabaseController::class, "index"]);
    Route::post('databases/flow', [DatabaseController::class, "syncFlow"]);
});
 