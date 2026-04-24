<?php

namespace Modules\Customer\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Customer\Repository\BrandRepositoryInterface;
use Modules\Customer\Repository\BrandRepository as LandlordBrandRepository;
use Modules\Customer\Repositories\Tenant\Contracts\BrandInterface;
use Modules\Customer\Repositories\Tenant\BrandRepository as TenantBrandRepository;
use Modules\Customer\Repository\BrandModuleSubscriptionRepositoryInterface;
use Modules\Customer\Repository\BrandModuleSubscriptionRepository;
use Modules\Customer\Repository\BranchRepositoryInterface;
use Modules\Customer\Repository\BranchRepository;

class CustomerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repository interfaces to their implementations
        $this->app->bind(BrandRepositoryInterface::class, LandlordBrandRepository::class);
        
        // Bind tenant brand interface to its implementation
        $this->app->bind(BrandInterface::class, TenantBrandRepository::class);
        
        $this->app->bind(BrandModuleSubscriptionRepositoryInterface::class, BrandModuleSubscriptionRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, BranchRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

