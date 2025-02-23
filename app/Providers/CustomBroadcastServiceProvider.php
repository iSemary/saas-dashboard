<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use App\Broadcasting\CustomRedisSocketBroadcaster;

class CustomBroadcastServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Broadcast::extend('redis', function ($app, $config) {
            return new CustomRedisSocketBroadcaster($config);
        });
    }
}