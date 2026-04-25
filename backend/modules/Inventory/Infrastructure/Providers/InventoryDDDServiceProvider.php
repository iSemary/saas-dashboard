<?php

namespace Modules\Inventory\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Inventory\Application\Services\InventoryService;
use Modules\Inventory\Domain\Contracts\StockMoveRepositoryInterface;
use Modules\Inventory\Domain\Contracts\WarehouseRepositoryInterface;
use Modules\Inventory\Infrastructure\Persistence\StockMoveRepository;
use Modules\Inventory\Infrastructure\Persistence\WarehouseRepository;

class InventoryDDDServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(StockMoveRepositoryInterface::class, StockMoveRepository::class);

        $this->app->bind(InventoryService::class, function ($app) {
            return new InventoryService(
                warehouseRepository: $app->make(WarehouseRepositoryInterface::class),
                stockMoveRepository: $app->make(StockMoveRepositoryInterface::class),
            );
        });
    }
}
