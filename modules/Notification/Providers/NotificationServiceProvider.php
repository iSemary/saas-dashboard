<?php

namespace Modules\Notification\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Notification\Repositories\NotificationInterface;
use Modules\Notification\Repositories\NotificationRepository;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(NotificationInterface::class, NotificationRepository::class);
    }

    public function boot() {}
}
