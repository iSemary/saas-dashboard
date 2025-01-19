<?php

use Illuminate\Support\Facades\Route;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'role:landlord', '2fa'])->group(function () {});
