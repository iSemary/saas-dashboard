<?php

namespace Modules\POS\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\POS\Application\Services\BarcodeService;
use Modules\POS\Application\Services\CategoryService;
use Modules\POS\Application\Services\DamagedService;
use Modules\POS\Application\Services\OfferPriceService;
use Modules\POS\Application\Services\PosDashboardService;
use Modules\POS\Application\Services\ProductService;
use Modules\POS\Application\Services\SubCategoryService;
use Modules\POS\Application\Services\TagService;
use Modules\POS\Domain\Contracts\BarcodeRepositoryInterface;
use Modules\POS\Domain\Contracts\CategoryRepositoryInterface;
use Modules\POS\Domain\Contracts\DamagedRepositoryInterface;
use Modules\POS\Domain\Contracts\OfferPriceRepositoryInterface;
use Modules\POS\Domain\Contracts\ProductRepositoryInterface;
use Modules\POS\Domain\Contracts\ProductStockRepositoryInterface;
use Modules\POS\Domain\Contracts\SubCategoryRepositoryInterface;
use Modules\POS\Domain\Contracts\TagRepositoryInterface;
use Modules\POS\Domain\Strategies\Pricing\OfferPricingStrategy;
use Modules\POS\Domain\Strategies\Pricing\RegularPricingStrategy;
use Modules\POS\Domain\Strategies\Pricing\WholesalePricingStrategy;
use Modules\POS\Domain\Strategies\Stock\DecrementStockStrategy;
use Modules\POS\Domain\Strategies\Stock\IncrementStockStrategy;
use Modules\POS\Infrastructure\Persistence\BarcodeRepository;
use Modules\POS\Infrastructure\Persistence\CategoryRepository;
use Modules\POS\Infrastructure\Persistence\DamagedRepository;
use Modules\POS\Infrastructure\Persistence\OfferPriceRepository;
use Modules\POS\Infrastructure\Persistence\ProductRepository;
use Modules\POS\Infrastructure\Persistence\ProductStockRepository;
use Modules\POS\Infrastructure\Persistence\SubCategoryRepository;
use Modules\POS\Infrastructure\Persistence\TagRepository;

class POSServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ─── Repository Bindings ─────────────────────────────────
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SubCategoryRepositoryInterface::class, SubCategoryRepository::class);
        $this->app->bind(BarcodeRepositoryInterface::class, BarcodeRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(OfferPriceRepositoryInterface::class, OfferPriceRepository::class);
        $this->app->bind(DamagedRepositoryInterface::class, DamagedRepository::class);
        $this->app->bind(ProductStockRepositoryInterface::class, ProductStockRepository::class);

        // ─── Strategy Bindings (tagged for resolution) ──────────
        $this->app->tag([
            RegularPricingStrategy::class,
            OfferPricingStrategy::class,
            WholesalePricingStrategy::class,
        ], 'pos.pricing.strategies');

        // ─── Application Services ────────────────────────────────
        $this->app->bind(ProductService::class, function ($app) {
            return new ProductService(
                repository:     $app->make(ProductRepositoryInterface::class),
                barcodeRepository: $app->make(BarcodeRepositoryInterface::class),
                stockRepository: $app->make(ProductStockRepositoryInterface::class),
                decrementStock: new DecrementStockStrategy($app->make(ProductStockRepositoryInterface::class)),
                incrementStock: new IncrementStockStrategy($app->make(ProductStockRepositoryInterface::class)),
            );
        });

        $this->app->bind(CategoryService::class, function ($app) {
            return new CategoryService(
                repository: $app->make(CategoryRepositoryInterface::class),
            );
        });

        $this->app->bind(SubCategoryService::class, function ($app) {
            return new SubCategoryService(
                repository: $app->make(SubCategoryRepositoryInterface::class),
            );
        });

        $this->app->bind(BarcodeService::class, function ($app) {
            return new BarcodeService(
                repository: $app->make(BarcodeRepositoryInterface::class),
            );
        });

        $this->app->bind(TagService::class, function ($app) {
            return new TagService(
                repository: $app->make(TagRepositoryInterface::class),
            );
        });

        $this->app->bind(OfferPriceService::class, function ($app) {
            $stock = $app->make(ProductStockRepositoryInterface::class);
            return new OfferPriceService(
                repository:        $app->make(OfferPriceRepositoryInterface::class),
                productRepository: $app->make(ProductRepositoryInterface::class),
                stockRepository:   $stock,
                decrementStock:    new DecrementStockStrategy($stock),
                incrementStock:    new IncrementStockStrategy($stock),
            );
        });

        $this->app->bind(DamagedService::class, function ($app) {
            $stock = $app->make(ProductStockRepositoryInterface::class);
            return new DamagedService(
                repository:     $app->make(DamagedRepositoryInterface::class),
                decrementStock: new DecrementStockStrategy($stock),
                incrementStock: new IncrementStockStrategy($stock),
            );
        });

        $this->app->singleton(PosDashboardService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path('POS', 'Database/migrations/tenant'));
    }
}
