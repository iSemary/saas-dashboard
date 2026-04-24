<?php

namespace Modules\Payment\Services;

use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\Gateways\StripeGateway;
use Modules\Payment\Gateways\PayPalGateway;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Cache;

class PaymentGatewayFactory
{
    protected array $gateways = [
        'stripe' => StripeGateway::class,
        'paypal' => PayPalGateway::class,
        // Add more gateways as they are implemented
        // 'razorpay' => RazorpayGateway::class,
        // 'adyen' => AdyenGateway::class,
        // 'square' => SquareGateway::class,
    ];

    protected array $instances = [];

    /**
     * Create a gateway instance for a payment method.
     *
     * @param PaymentMethod $paymentMethod
     * @param string $environment
     * @return PaymentGatewayInterface
     * @throws PaymentGatewayException
     */
    public function create(PaymentMethod $paymentMethod, string $environment = 'production'): PaymentGatewayInterface
    {
        $cacheKey = "gateway_{$paymentMethod->id}_{$environment}";
        
        if (isset($this->instances[$cacheKey])) {
            return $this->instances[$cacheKey];
        }

        $gatewayClass = $this->getGatewayClass($paymentMethod->processor_type);
        $config = $this->buildGatewayConfig($paymentMethod, $environment);
        
        $gateway = new $gatewayClass($config);
        $gateway->setTestMode($environment === 'sandbox');
        
        $this->instances[$cacheKey] = $gateway;
        
        return $gateway;
    }

    /**
     * Create a gateway instance by processor type.
     *
     * @param string $processorType
     * @param array $config
     * @param bool $testMode
     * @return PaymentGatewayInterface
     * @throws PaymentGatewayException
     */
    public function createByType(string $processorType, array $config = [], bool $testMode = false): PaymentGatewayInterface
    {
        $gatewayClass = $this->getGatewayClass($processorType);
        
        $gateway = new $gatewayClass($config);
        $gateway->setTestMode($testMode);
        
        return $gateway;
    }

    /**
     * Get available gateway types.
     *
     * @return array
     */
    public function getAvailableGateways(): array
    {
        return array_keys($this->gateways);
    }

    /**
     * Check if a gateway type is supported.
     *
     * @param string $processorType
     * @return bool
     */
    public function isSupported(string $processorType): bool
    {
        return isset($this->gateways[$processorType]);
    }

    /**
     * Register a new gateway.
     *
     * @param string $name
     * @param string $className
     * @return void
     */
    public function register(string $name, string $className): void
    {
        if (!class_exists($className)) {
            throw new PaymentGatewayException("Gateway class {$className} does not exist");
        }

        if (!in_array(PaymentGatewayInterface::class, class_implements($className))) {
            throw new PaymentGatewayException("Gateway class {$className} must implement PaymentGatewayInterface");
        }

        $this->gateways[$name] = $className;
    }

    /**
     * Get gateway information.
     *
     * @param string $processorType
     * @return array
     */
    public function getGatewayInfo(string $processorType): array
    {
        if (!$this->isSupported($processorType)) {
            throw new PaymentGatewayException("Unsupported gateway: {$processorType}");
        }

        $gatewayClass = $this->gateways[$processorType];
        $gateway = new $gatewayClass();

        return [
            'name' => $gateway->getName(),
            'version' => $gateway->getVersion(),
            'supported_currencies' => $gateway->getSupportedCurrencies(),
            'supported_countries' => $gateway->getSupportedCountries(),
            'supported_features' => $this->getGatewayFeatures($gateway),
            'configuration_requirements' => $gateway->getConfigurationRequirements(),
        ];
    }

    /**
     * Get all gateway information.
     *
     * @return array
     */
    public function getAllGatewayInfo(): array
    {
        $info = [];
        
        foreach ($this->getAvailableGateways() as $gatewayType) {
            $info[$gatewayType] = $this->getGatewayInfo($gatewayType);
        }
        
        return $info;
    }

    /**
     * Test gateway connection.
     *
     * @param PaymentMethod $paymentMethod
     * @param string $environment
     * @return bool
     */
    public function testConnection(PaymentMethod $paymentMethod, string $environment = 'production'): bool
    {
        try {
            $gateway = $this->create($paymentMethod, $environment);
            return $gateway->testConnection();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validate gateway configuration.
     *
     * @param PaymentMethod $paymentMethod
     * @param string $environment
     * @return array
     */
    public function validateConfiguration(PaymentMethod $paymentMethod, string $environment = 'production'): array
    {
        $errors = [];
        
        try {
            $gateway = $this->create($paymentMethod, $environment);
            $requirements = $gateway->getConfigurationRequirements();
            $config = $this->buildGatewayConfig($paymentMethod, $environment);
            
            foreach ($requirements as $key => $requirement) {
                if ($requirement['required'] && empty($config[$key])) {
                    $errors[] = "Missing required configuration: {$key}";
                }
            }
            
            if (empty($errors) && !$gateway->testConnection()) {
                $errors[] = "Gateway connection test failed";
            }
            
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        return $errors;
    }

    /**
     * Clear gateway instances cache.
     *
     * @return void
     */
    public function clearCache(): void
    {
        $this->instances = [];
    }

    /**
     * Get gateway class for processor type.
     *
     * @param string $processorType
     * @return string
     * @throws PaymentGatewayException
     */
    protected function getGatewayClass(string $processorType): string
    {
        if (!isset($this->gateways[$processorType])) {
            throw new PaymentGatewayException("Unsupported gateway: {$processorType}");
        }

        $gatewayClass = $this->gateways[$processorType];
        
        if (!class_exists($gatewayClass)) {
            throw new PaymentGatewayException("Gateway class not found: {$gatewayClass}");
        }

        return $gatewayClass;
    }

    /**
     * Build gateway configuration from payment method.
     *
     * @param PaymentMethod $paymentMethod
     * @param string $environment
     * @return array
     */
    protected function buildGatewayConfig(PaymentMethod $paymentMethod, string $environment): array
    {
        $config = [
            'payment_method_id' => $paymentMethod->id,
            'test_mode' => $environment === 'sandbox',
        ];

        // Get configurations for the specified environment
        $configurations = $paymentMethod->configurations()
                                       ->where('environment', $environment)
                                       ->where('status', 'active')
                                       ->get();

        foreach ($configurations as $configuration) {
            $config[$configuration->config_key] = $configuration->getTypedValue();
        }

        return $config;
    }

    /**
     * Get gateway features.
     *
     * @param PaymentGatewayInterface $gateway
     * @return array
     */
    protected function getGatewayFeatures(PaymentGatewayInterface $gateway): array
    {
        $features = [];
        $possibleFeatures = [
            'payments',
            'refunds',
            'partial_refunds',
            'authorization',
            'capture',
            'void',
            'recurring',
            'customers',
            'payment_methods',
            'webhooks',
            'disputes',
            'connect',
            'marketplace',
        ];

        foreach ($possibleFeatures as $feature) {
            if ($gateway->supportsFeature($feature)) {
                $features[] = $feature;
            }
        }

        return $features;
    }

    /**
     * Get recommended gateways for a country and currency.
     *
     * @param string $country
     * @param string $currency
     * @return array
     */
    public function getRecommendedGateways(string $country, string $currency): array
    {
        $recommended = [];
        
        foreach ($this->getAvailableGateways() as $gatewayType) {
            try {
                $info = $this->getGatewayInfo($gatewayType);
                
                $supportsCurrency = in_array($currency, $info['supported_currencies']);
                $supportsCountry = in_array($country, $info['supported_countries']);
                
                if ($supportsCurrency && $supportsCountry) {
                    $recommended[] = [
                        'type' => $gatewayType,
                        'name' => $info['name'],
                        'features' => $info['supported_features'],
                        'score' => $this->calculateGatewayScore($gatewayType, $country, $currency),
                    ];
                }
            } catch (\Exception $e) {
                // Skip gateways that can't be loaded
                continue;
            }
        }
        
        // Sort by score (highest first)
        usort($recommended, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return $recommended;
    }

    /**
     * Calculate gateway score for recommendation.
     *
     * @param string $gatewayType
     * @param string $country
     * @param string $currency
     * @return int
     */
    protected function calculateGatewayScore(string $gatewayType, string $country, string $currency): int
    {
        $score = 0;
        
        // Base scores for different gateways
        $baseScores = [
            'stripe' => 90,
            'paypal' => 85,
            'razorpay' => 80,
            'adyen' => 85,
            'square' => 75,
        ];
        
        $score += $baseScores[$gatewayType] ?? 50;
        
        // Regional preferences
        $regionalPreferences = [
            'US' => ['stripe' => 10, 'paypal' => 5, 'square' => 8],
            'IN' => ['razorpay' => 15, 'stripe' => 5],
            'GB' => ['stripe' => 8, 'adyen' => 10],
            'EU' => ['stripe' => 8, 'adyen' => 12],
        ];
        
        if (isset($regionalPreferences[$country][$gatewayType])) {
            $score += $regionalPreferences[$country][$gatewayType];
        }
        
        // Currency preferences
        $currencyPreferences = [
            'USD' => ['stripe' => 5, 'paypal' => 8],
            'EUR' => ['stripe' => 5, 'adyen' => 8],
            'INR' => ['razorpay' => 10],
        ];
        
        if (isset($currencyPreferences[$currency][$gatewayType])) {
            $score += $currencyPreferences[$currency][$gatewayType];
        }
        
        return $score;
    }
}
