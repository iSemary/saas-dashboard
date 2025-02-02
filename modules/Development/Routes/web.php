<?php

use Illuminate\Support\Facades\Route;
use Modules\Development\Http\Controllers\ConfigurationController;
use Modules\Development\Http\Controllers\FlowController;
use Modules\Development\Http\Controllers\IpBlacklistController;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {
    // DEVELOPERS ONLY
    Route::prefix('development')->name('development.')->group(function () {
        Route::resource('configurations', ConfigurationController::class)->names('configurations');
        
        Route::resource('ip-blacklists', IpBlacklistController::class)->names('ip-blacklists');

        // Flows 
        Route::get('flows/modules', [FlowController::class, "modules"])->name('flows.modules');
        Route::get('flows/database', [FlowController::class, "database"])->name('flows.database');
    });
});
