<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.User.{id}', function ($user, $id) {
    Log::info("Channels.php {$user->id} | $id");
    return $user->id === $id;
});

Broadcast::channel('user.notification.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
