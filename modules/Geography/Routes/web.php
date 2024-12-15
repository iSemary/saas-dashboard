<?php

use Illuminate\Support\Facades\Route;
use Modules\Geography\Http\Controllers\CountryController;
use Modules\Geography\Http\Controllers\CityController;
use Modules\Geography\Http\Controllers\TownController;
use Modules\Geography\Http\Controllers\ProvinceController;


Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {
    Route::resource('countries', CountryController::class)->names('countries');
    Route::resource('cities', CityController::class)->names('cities');
    Route::resource('towns', TownController::class)->names('towns');
    Route::resource('provinces', ProvinceController::class)->names('provinces');
});
