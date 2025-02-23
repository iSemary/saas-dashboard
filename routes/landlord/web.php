<?php

use App\Events\NotificationEvent;
use Illuminate\Support\Facades\Route;

Route::prefix('landlord')->name('landlord.')->middleware(['auth:web', 'landlord_roles', '2fa'])->group(function () {
    Route::get("test/event", function () {
        broadcast(new NotificationEvent(auth()->user()->id, ["title" => "Hello From Laravel", "message" => "Hello From Laravel New Event From Test"]));
    });
});
