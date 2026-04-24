<?php

namespace Modules\Payment\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Payment\Repositories\PaymentMethodInterface;
use Modules\Payment\Repositories\PaymentMethodRepository;
use Modules\Payment\Repositories\PaymentTransactionInterface;
use Modules\Payment\Repositories\PaymentTransactionRepository;
use Modules\Payment\Services\PaymentGatewayFactory;
use Modules\Payment\Services\PaymentGatewayService;
use Modules\Payment\Services\CurrencyConversionService;
use Modules\Payment\Services\FeeCalculationService;
use Modules\Payment\Services\PaymentRoutingService;
use Modules\Payment\Services\PaymentValidationService;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces
        $this->app->bind(PaymentMethodInterface::class, PaymentMethodRepository::class);
        $this->app->bind(PaymentTransactionInterface::class, PaymentTransactionRepository::class);
        
        // Register services as singletons
        $this->app->singleton(PaymentGatewayFactory::class, function ($app) {
            return new PaymentGatewayFactory();
        });
        
        $this->app->singleton(CurrencyConversionService::class, function ($app) {
            return new CurrencyConversionService();
        });
        
        $this->app->singleton(FeeCalculationService::class, function ($app) {
            return new FeeCalculationService();
        });
        
        $this->app->singleton(PaymentRoutingService::class, function ($app) {
            return new PaymentRoutingService();
        });
        
        $this->app->singleton(PaymentValidationService::class, function ($app) {
            return new PaymentValidationService();
        });
        
        $this->app->singleton(PaymentGatewayService::class, function ($app) {
            return new PaymentGatewayService(
                $app->make(PaymentGatewayFactory::class),
                $app->make(PaymentRoutingService::class),
                $app->make(FeeCalculationService::class),
                $app->make(CurrencyConversionService::class),
                $app->make(PaymentValidationService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'payment');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
        
        // Publish config
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('payment.php'),
        ], 'payment-config');
        
        // Publish views
        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('views/vendor/payment'),
        ], 'payment-views');
    }
}
