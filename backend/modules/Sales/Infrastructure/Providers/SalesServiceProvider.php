<?php

namespace Modules\Sales\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Sales\Application\Services\SalesOrderService;
use Modules\Sales\Domain\Contracts\SalesOrderRepositoryInterface;
use Modules\Sales\Infrastructure\Persistence\SalesOrderRepository;

class SalesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SalesOrderRepositoryInterface::class, SalesOrderRepository::class);

        $this->app->bind(SalesOrderService::class, function ($app) {
            return new SalesOrderService(
                repository:      $app->make(SalesOrderRepositoryInterface::class),
                stockRepository: $app->make(\Modules\POS\Domain\Contracts\ProductStockRepositoryInterface::class),
            );
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('Sales', 'Database/migrations/tenant'));
    }
}
