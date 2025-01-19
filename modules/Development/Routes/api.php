<?php

use Illuminate\Support\Facades\Route;
use Modules\Development\Http\Controllers\Api\DevelopmentController;

// TODO Add auth middleware
Route::prefix('development')->group(function () {
    Route::get('routes', [DevelopmentController::class, "routes"]);
});
