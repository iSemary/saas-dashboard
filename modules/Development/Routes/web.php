<?php

use Illuminate\Support\Facades\Route;
use Modules\Development\Http\Controllers\ConfigurationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    // DEVELOPERS ONLY
    Route::prefix('development')->name('development.')->group(function () {
        Route::resource('configurations', ConfigurationController::class)->names('configurations');
    });
});
