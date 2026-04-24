<?php

namespace Modules\Subscription\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Subscription\Repositories\PlanInterface;
use Modules\Subscription\Repositories\PlanRepository;
use Modules\Subscription\Repositories\SubscriptionInterface;
use Modules\Subscription\Repositories\SubscriptionRepository;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PlanInterface::class, PlanRepository::class);
        $this->app->bind(SubscriptionInterface::class, SubscriptionRepository::class);
    }

    public function boot() {}
}
