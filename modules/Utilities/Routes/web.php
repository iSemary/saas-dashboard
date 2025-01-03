<?php

use Illuminate\Support\Facades\Route;
use Modules\Utilities\Http\Controllers\UtilitiesController;
use Modules\Utilities\Http\Controllers\CurrencyController;

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
    Route::resource('categories', UtilitiesController::class)->names('categories');
    Route::resource('currencies', CurrencyController::class)->names('currencies');
    Route::resource('tags', UtilitiesController::class)->names('tags');
    Route::resource('announcements', UtilitiesController::class)->names('announcements');
    Route::resource('modules', UtilitiesController::class)->names('modules');
});
