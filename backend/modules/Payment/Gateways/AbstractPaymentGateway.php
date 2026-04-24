<?php

namespace Modules\Payment\Gateways;

use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\PaymentRequest;
use Modules\Payment\DTOs\PaymentResponse;
use Modules\Payment\DTOs\RefundRequest;
use Modules\Payment\DTOs\RefundResponse;
use Modules\Payment\DTOs\CustomerRequest;
use Modules\Payment\DTOs\CustomerResponse;
use Modules\Payment\DTOs\WebhookRequest;
use Modules\Payment\DTOs\WebhookResponse;
use Modules\Payment\Entities\PaymentGatewayLog;
use Modules\Payment\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected array $config = [];
    protected bool $testMode = false;
    protected string $name;
    protected string $version = '1.0.0';
    protected array $supportedFeatures = [];
    protected array $supportedCurrencies = [];
    protected array $supportedCountries = [];
    protected int $timeout = 30;
    protected array $defaultHeaders = [];

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfiguration($config);
        $this->initialize();
    }

    /**
     * Initialize the gateway.
     */
    abstract protected function initialize(): void;

    /**
     * Get the base API URL.
     */
    abstract protected function getBaseUrl(): string;

    /**
     * Get authentication headers.
     */
    abstract protected function getAuthHeaders(): array;

    /**
     * Transform payment request to gateway format.
     */
    abstract protected function transformPaymentRequest(PaymentRequest $request): array;

    /**
     * Transform gateway response to standard format.
     */
    abstract protected function transformPaymentResponse(array $response): PaymentResponse;

    /**
     * Transform refund request to gateway format.
     */
    abstract protected function transformRefundRequest(RefundRequest $request): array;

    /**
     * Transform gateway refund response to standard format.
     */
    abstract protected function transformRefundResponse(array $response): RefundResponse;

    /**
     * Process a payment transaction.
     */
    public function processPayment(PaymentRequest $request): PaymentResponse
    {
        $startTime = microtime(true);
        $correlationId = $this->generateCorrelationId();

        try {
            $this->validatePaymentRequest($request);
            
            $gatewayRequest = $this->transformPaymentRequest($request);
            $endpoint = $this->getPaymentEndpoint();
            
            $this->logRequest('payment', $endpoint, $gatewayRequest, $correlationId);
            
            $response = $this->makeHttpRequest('POST', $endpoint, $gatewayRequest);
            $processingTime = (microtime(true) - $startTime) * 1000;
            
            $this->logResponse('payment', $response, $processingTime, $correlationId);
            
            return $this->transformPaymentResponse($response);
            
        } catch (\Exception $e) {
            $processingTime = (microtime(true) - $startTime) * 1000;
            $this->logError('payment', $e->getMessage(), $processingTime, $correlationId);
            
            throw new PaymentGatewayException(
                "Payment processing failed: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Authorize a payment (without capturing).
     */
    public function authorizePayment(PaymentRequest $request): PaymentResponse
    {
        $request->setCapture(false);
        return $this->processPayment($request);
    }

    /**
     * Capture a previously authorized payment.
     */
    public function capturePayment(string $authorizationId, float $amount, array $metadata = []): PaymentResponse
    {
        $startTime = microtime(true);
        $correlationId = $this->generateCorrelationId();

        try {
            $gatewayRequest = $this->transformCaptureRequest($authorizationId, $amount, $metadata);
            $endpoint = $this->getCaptureEndpoint($authorizationId);
            
            $this->logRequest('capture', $endpoint, $gatewayRequest, $correlationId);
            
            $response = $this->makeHttpRequest('POST', $endpoint, $gatewayRequest);
            $processingTime = (microtime(true) - $startTime) * 1000;
            
            $this->logResponse('capture', $response, $processingTime, $correlationId);
            
            return $this->transformPaymentResponse($response);
            
        } catch (\Exception $e) {
            $processingTime = (microtime(true) - $startTime) * 1000;
            $this->logError('capture', $e->getMessage(), $processingTime, $correlationId);
            
            throw new PaymentGatewayException(
                "Payment capture failed: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Void an authorized payment.
     */
    public function voidPayment(string $authorizationId, array $metadata = []): PaymentResponse
    {
        $startTime = microtime(true);
        $correlationId = $this->generateCorrelationId();

        try {
            $gatewayRequest = $this->transformVoidRequest($authorizationId, $metadata);
            $endpoint = $this->getVoidEndpoint($authorizationId);
            
            $this->logRequest('void', $endpoint, $gatewayRequest, $correlationId);
            
            $response = $this->makeHttpRequest('POST', $endpoint, $gatewayRequest);
            $processingTime = (microtime(true) - $startTime) * 1000;
            
            $this->logResponse('void', $response, $processingTime, $correlationId);
            
            return $this->transformPaymentResponse($response);
            
        } catch (\Exception $e) {
            $processingTime = (microtime(true) - $startTime) * 1000;
            $this->logError('void', $e->getMessage(), $processingTime, $correlationId);
            
            throw new PaymentGatewayException(
                "Payment void failed: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Process a refund.
     */
    public function processRefund(RefundRequest $request): RefundResponse
    {
        $startTime = microtime(true);
        $correlationId = $this->generateCorrelationId();

        try {
            $this->validateRefundRequest($request);
            
            $gatewayRequest = $this->transformRefundRequest($request);
            $endpoint = $this->getRefundEndpoint();
            
            $this->logRequest('refund', $endpoint, $gatewayRequest, $correlationId);
            
            $response = $this->makeHttpRequest('POST', $endpoint, $gatewayRequest);
            $processingTime = (microtime(true) - $startTime) * 1000;
            
            $this->logResponse('refund', $response, $processingTime, $correlationId);
            
            return $this->transformRefundResponse($response);
            
        } catch (\Exception $e) {
            $processingTime = (microtime(true) - $startTime) * 1000;
            $this->logError('refund', $e->getMessage(), $processingTime, $correlationId);
            
            throw new PaymentGatewayException(
                "Refund processing failed: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Make HTTP request to gateway.
     */
    protected function makeHttpRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->getBaseUrl() . '/' . ltrim($endpoint, '/');
        $headers = array_merge($this->defaultHeaders, $this->getAuthHeaders());

        $response = Http::withHeaders($headers)
                       ->timeout($this->timeout)
                       ->$method($url, $data);

        if (!$response->successful()) {
            throw new PaymentGatewayException(
                "HTTP request failed: " . $response->body(),
                $response->status()
            );
        }

        return $response->json();
    }

    /**
     * Validate payment request.
     */
    protected function validatePaymentRequest(PaymentRequest $request): void
    {
        if (!$request->getAmount() || $request->getAmount() <= 0) {
            throw new PaymentGatewayException("Invalid payment amount");
        }

        if (!$request->getCurrency()) {
            throw new PaymentGatewayException("Currency is required");
        }

        if (!in_array($request->getCurrency(), $this->getSupportedCurrencies())) {
            throw new PaymentGatewayException("Currency not supported: " . $request->getCurrency());
        }
    }

    /**
     * Validate refund request.
     */
    protected function validateRefundRequest(RefundRequest $request): void
    {
        if (!$request->getTransactionId()) {
            throw new PaymentGatewayException("Transaction ID is required for refund");
        }

        if (!$request->getAmount() || $request->getAmount() <= 0) {
            throw new PaymentGatewayException("Invalid refund amount");
        }
    }

    /**
     * Log request.
     */
    protected function logRequest(string $operation, string $endpoint, array $data, string $correlationId): void
    {
        PaymentGatewayLog::createLog($this->getPaymentMethodId(), 'info', $operation, [
            'endpoint_called' => $endpoint,
            'request_data' => $data,
            'correlation_id' => $correlationId,
        ]);
    }

    /**
     * Log response.
     */
    protected function logResponse(string $operation, array $response, float $processingTime, string $correlationId): void
    {
        PaymentGatewayLog::createLog($this->getPaymentMethodId(), 'info', $operation, [
            'response_data' => $response,
            'processing_time_ms' => round($processingTime),
            'correlation_id' => $correlationId,
        ]);
    }

    /**
     * Log error.
     */
    protected function logError(string $operation, string $error, float $processingTime, string $correlationId): void
    {
        PaymentGatewayLog::createLog($this->getPaymentMethodId(), 'error', $operation, [
            'error_message' => $error,
            'processing_time_ms' => round($processingTime),
            'correlation_id' => $correlationId,
        ]);
    }

    /**
     * Generate correlation ID.
     */
    protected function generateCorrelationId(): string
    {
        return uniqid($this->getName() . '_', true);
    }

    /**
     * Get payment method ID from configuration.
     */
    protected function getPaymentMethodId(): ?int
    {
        return $this->config['payment_method_id'] ?? null;
    }

    /**
     * Get gateway name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get gateway version.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Set gateway configuration.
     */
    public function setConfiguration(array $config): void
    {
        $this->config = array_merge($this->config, $config);
        
        if (isset($config['test_mode'])) {
            $this->setTestMode($config['test_mode']);
        }
    }

    /**
     * Get gateway configuration.
     */
    public function getConfiguration(): array
    {
        return $this->config;
    }

    /**
     * Set test mode.
     */
    public function setTestMode(bool $testMode): void
    {
        $this->testMode = $testMode;
    }

    /**
     * Check if gateway is in test mode.
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    /**
     * Get supported currencies.
     */
    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }

    /**
     * Get supported countries.
     */
    public function getSupportedCountries(): array
    {
        return $this->supportedCountries;
    }

    /**
     * Check if the gateway supports a specific feature.
     */
    public function supportsFeature(string $feature): bool
    {
        return in_array($feature, $this->supportedFeatures);
    }

    /**
     * Test the gateway connection and configuration.
     */
    public function testConnection(): bool
    {
        try {
            $endpoint = $this->getTestEndpoint();
            $response = $this->makeHttpRequest('GET', $endpoint);
            return isset($response['status']) && $response['status'] === 'ok';
        } catch (\Exception $e) {
            Log::error("Gateway connection test failed: " . $e->getMessage());
            return false;
        }
    }

    // Abstract methods that must be implemented by concrete gateways

    abstract protected function getPaymentEndpoint(): string;
    abstract protected function getRefundEndpoint(): string;
    abstract protected function getCaptureEndpoint(string $authorizationId): string;
    abstract protected function getVoidEndpoint(string $authorizationId): string;
    abstract protected function getTestEndpoint(): string;
    abstract protected function transformCaptureRequest(string $authorizationId, float $amount, array $metadata): array;
    abstract protected function transformVoidRequest(string $authorizationId, array $metadata): array;
}
