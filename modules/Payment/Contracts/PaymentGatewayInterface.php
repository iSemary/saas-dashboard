<?php

namespace Modules\Payment\Contracts;

use Modules\Payment\DTOs\PaymentRequest;
use Modules\Payment\DTOs\PaymentResponse;
use Modules\Payment\DTOs\RefundRequest;
use Modules\Payment\DTOs\RefundResponse;
use Modules\Payment\DTOs\CustomerRequest;
use Modules\Payment\DTOs\CustomerResponse;
use Modules\Payment\DTOs\WebhookRequest;
use Modules\Payment\DTOs\WebhookResponse;

interface PaymentGatewayInterface
{
    /**
     * Process a payment transaction.
     *
     * @param PaymentRequest $request
     * @return PaymentResponse
     */
    public function processPayment(PaymentRequest $request): PaymentResponse;

    /**
     * Authorize a payment (without capturing).
     *
     * @param PaymentRequest $request
     * @return PaymentResponse
     */
    public function authorizePayment(PaymentRequest $request): PaymentResponse;

    /**
     * Capture a previously authorized payment.
     *
     * @param string $authorizationId
     * @param float $amount
     * @param array $metadata
     * @return PaymentResponse
     */
    public function capturePayment(string $authorizationId, float $amount, array $metadata = []): PaymentResponse;

    /**
     * Void an authorized payment.
     *
     * @param string $authorizationId
     * @param array $metadata
     * @return PaymentResponse
     */
    public function voidPayment(string $authorizationId, array $metadata = []): PaymentResponse;

    /**
     * Process a refund.
     *
     * @param RefundRequest $request
     * @return RefundResponse
     */
    public function processRefund(RefundRequest $request): RefundResponse;

    /**
     * Create a customer in the gateway.
     *
     * @param CustomerRequest $request
     * @return CustomerResponse
     */
    public function createCustomer(CustomerRequest $request): CustomerResponse;

    /**
     * Update a customer in the gateway.
     *
     * @param string $customerId
     * @param CustomerRequest $request
     * @return CustomerResponse
     */
    public function updateCustomer(string $customerId, CustomerRequest $request): CustomerResponse;

    /**
     * Delete a customer from the gateway.
     *
     * @param string $customerId
     * @return bool
     */
    public function deleteCustomer(string $customerId): bool;

    /**
     * Get customer details from the gateway.
     *
     * @param string $customerId
     * @return CustomerResponse
     */
    public function getCustomer(string $customerId): CustomerResponse;

    /**
     * Create a payment method for a customer.
     *
     * @param string $customerId
     * @param array $paymentMethodData
     * @return array
     */
    public function createPaymentMethod(string $customerId, array $paymentMethodData): array;

    /**
     * Get payment methods for a customer.
     *
     * @param string $customerId
     * @return array
     */
    public function getPaymentMethods(string $customerId): array;

    /**
     * Delete a payment method.
     *
     * @param string $paymentMethodId
     * @return bool
     */
    public function deletePaymentMethod(string $paymentMethodId): bool;

    /**
     * Handle webhook requests.
     *
     * @param WebhookRequest $request
     * @return WebhookResponse
     */
    public function handleWebhook(WebhookRequest $request): WebhookResponse;

    /**
     * Verify webhook signature.
     *
     * @param string $payload
     * @param string $signature
     * @param string $secret
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool;

    /**
     * Get transaction details from the gateway.
     *
     * @param string $transactionId
     * @return array
     */
    public function getTransaction(string $transactionId): array;

    /**
     * Get supported currencies for this gateway.
     *
     * @return array
     */
    public function getSupportedCurrencies(): array;

    /**
     * Get supported countries for this gateway.
     *
     * @return array
     */
    public function getSupportedCountries(): array;

    /**
     * Check if the gateway supports a specific feature.
     *
     * @param string $feature
     * @return bool
     */
    public function supportsFeature(string $feature): bool;

    /**
     * Get gateway-specific configuration requirements.
     *
     * @return array
     */
    public function getConfigurationRequirements(): array;

    /**
     * Test the gateway connection and configuration.
     *
     * @return bool
     */
    public function testConnection(): bool;

    /**
     * Get gateway name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get gateway version.
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Set gateway configuration.
     *
     * @param array $config
     * @return void
     */
    public function setConfiguration(array $config): void;

    /**
     * Get gateway configuration.
     *
     * @return array
     */
    public function getConfiguration(): array;

    /**
     * Set test mode.
     *
     * @param bool $testMode
     * @return void
     */
    public function setTestMode(bool $testMode): void;

    /**
     * Check if gateway is in test mode.
     *
     * @return bool
     */
    public function isTestMode(): bool;
}
