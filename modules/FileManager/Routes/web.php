<?php

use Illuminate\Support\Facades\Route;
use Modules\FileManager\Http\Controllers\FileController;


Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    // DEVELOPERS ONLY
    Route::prefix('development')->name('development.')->group(function () {
        Route::get('files/manage', [FileController::class, 'manager'])->name('files.manage');
        Route::resource('files', FileController::class)->names('files');
    });
});
