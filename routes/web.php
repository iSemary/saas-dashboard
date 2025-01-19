<?php

use App\Events\TestNotification;
use Illuminate\Support\Facades\Route;

Route::get('channels/test', function () {
    event(new TestNotification());
});
