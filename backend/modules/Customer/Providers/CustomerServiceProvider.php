<?php

namespace Modules\Customer\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Customer\Repository\BrandRepositoryInterface;
use Modules\Customer\Repository\BrandRepository as LandlordBrandRepository;
use Modules\Customer\Repositories\Tenant\Contracts\BrandInterface;
use Modules\Customer\Repository\Tenant\BrandRepository as TenantBrandRepository;
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
        // Bind repository interfaces to their implementations based on context
        $this->app->bind(BrandRepositoryInterface::class, function ($app) {
            $subdomain = \Modules\Tenant\Helper\TenantHelper::getSubDomain();
            // Use tenant repository for non-landlord subdomains
            if ($subdomain && $subdomain !== 'landlord') {
                return new TenantBrandRepository(new \Modules\Customer\Entities\Tenant\Brand());
            }
            return new LandlordBrandRepository();
        });

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

