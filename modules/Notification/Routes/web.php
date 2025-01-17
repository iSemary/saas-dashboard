<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;


Route::middleware(['auth:web', '2fa'])->group(function () {
    Route::resource('notifications', NotificationController::class)->names('notifications');
});
