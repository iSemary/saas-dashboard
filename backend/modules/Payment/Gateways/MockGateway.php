<?php

namespace Modules\Payment\Gateways;

use Modules\Payment\DTOs\PaymentRequest;
use Modules\Payment\DTOs\PaymentResponse;
use Modules\Payment\DTOs\RefundRequest;
use Modules\Payment\DTOs\RefundResponse;
use Modules\Payment\DTOs\CustomerRequest;
use Modules\Payment\DTOs\CustomerResponse;
use Modules\Payment\DTOs\WebhookRequest;
use Modules\Payment\DTOs\WebhookResponse;

class MockGateway extends AbstractPaymentGateway
{
    protected array $processedTransactions = [];
    protected array $customers = [];

    /**
     * Test card numbers for different scenarios.
     */
    protected array $testCards = [
        '4242424242424242' => 'success',
        '4000000000000002' => 'card_declined',
        '4000000000009995' => 'insufficient_funds',
        '4000000000000069' => 'expired_card',
        '4000000000000127' => 'incorrect_cvc',
        '4000000000000119' => 'processing_error',
    ];

    public function processPayment(PaymentRequest $request): PaymentResponse
    {
        // Simulate processing time
        usleep(rand(100000, 500000)); // 100-500ms

        $transactionId = 'mock_txn_' . uniqid() . '_' . time();
        $gatewayTransactionId = 'mock_gw_' . uniqid();

        // Check for test card scenarios
        $cardNumber = $this->extractCardNumber($request);
        $scenario = $this->testCards[$cardNumber] ?? 'success';

        switch ($scenario) {
            case 'card_declined':
                return new PaymentResponse(
                    false,
                    $transactionId,
                    null,
                    $request->getAmount(),
                    $request->getCurrency(),
                    'failed',
                    'CARD_DECLINED',
                    'Your card was declined'
                );

            case 'insufficient_funds':
                return new PaymentResponse(
                    false,
                    $transactionId,
                    null,
                    $request->getAmount(),
                    $request->getCurrency(),
                    'failed',
                    'INSUFFICIENT_FUNDS',
                    'Your card has insufficient funds'
                );

            case 'expired_card':
                return new PaymentResponse(
                    false,
                    $transactionId,
                    null,
                    $request->getAmount(),
                    $request->getCurrency(),
                    'failed',
                    'EXPIRED_CARD',
                    'Your card has expired'
                );

            case 'incorrect_cvc':
                return new PaymentResponse(
                    false,
                    $transactionId,
                    null,
                    $request->getAmount(),
                    $request->getCurrency(),
                    'failed',
                    'INCORRECT_CVC',
                    'Your card\'s security code is incorrect'
                );

            case 'processing_error':
                return new PaymentResponse(
                    false,
                    $transactionId,
                    null,
                    $request->getAmount(),
                    $request->getCurrency(),
                    'failed',
                    'PROCESSING_ERROR',
                    'An error occurred while processing your payment'
                );

            default: // success
                // Store transaction for refund testing
                $this->processedTransactions[$gatewayTransactionId] = [
                    'amount' => $request->getAmount(),
                    'currency' => $request->getCurrency(),
                    'refunded_amount' => 0,
                    'status' => 'completed',
                ];

                return new PaymentResponse(
                    true,
                    $transactionId,
                    $gatewayTransactionId,
                    $request->getAmount(),
                    $request->getCurrency(),
                    'completed'
                );
        }
    }

    public function refund(RefundRequest $request): RefundResponse
    {
        // Simulate processing time
        usleep(rand(50000, 200000)); // 50-200ms

        $refundId = 'mock_ref_' . uniqid();
        $transactionId = $request->getTransactionId();

        // Check if transaction exists
        if (!isset($this->processedTransactions[$transactionId])) {
            return new RefundResponse(
                false,
                null,
                $request->getAmount(),
                'TRANSACTION_NOT_FOUND',
                'Original transaction not found'
            );
        }

        $originalTransaction = $this->processedTransactions[$transactionId];
        $totalRefunded = $originalTransaction['refunded_amount'] + $request->getAmount();

        // Check if refund amount exceeds original
        if ($totalRefunded > $originalTransaction['amount']) {
            return new RefundResponse(
                false,
                null,
                $request->getAmount(),
                'REFUND_AMOUNT_EXCEEDS_ORIGINAL',
                'Refund amount exceeds original transaction amount'
            );
        }

        // Update refunded amount
        $this->processedTransactions[$transactionId]['refunded_amount'] = $totalRefunded;

        return new RefundResponse(
            true,
            $refundId,
            $request->getAmount()
        );
    }

    public function createCustomer(CustomerRequest $request): CustomerResponse
    {
        // Simulate processing time
        usleep(rand(100000, 300000)); // 100-300ms

        $customerId = 'mock_cust_' . uniqid();

        // Store customer
        $this->customers[$customerId] = [
            'email' => $request->getEmail(),
            'name' => $request->getName(),
            'phone' => $request->getPhone(),
            'address' => $request->getAddress(),
            'created_at' => now(),
        ];

        return new CustomerResponse(
            true,
            $customerId,
            $request->getEmail(),
            $request->getName()
        );
    }

    public function updateCustomer(string $customerId, CustomerRequest $request): CustomerResponse
    {
        // Simulate processing time
        usleep(rand(100000, 300000));

        if (!isset($this->customers[$customerId])) {
            return new CustomerResponse(
                false,
                null,
                null,
                null,
                'CUSTOMER_NOT_FOUND',
                'Customer not found'
            );
        }

        // Update customer
        $this->customers[$customerId] = array_merge($this->customers[$customerId], [
            'email' => $request->getEmail(),
            'name' => $request->getName(),
            'phone' => $request->getPhone(),
            'address' => $request->getAddress(),
            'updated_at' => now(),
        ]);

        return new CustomerResponse(
            true,
            $customerId,
            $request->getEmail(),
            $request->getName()
        );
    }

    public function retrieveCustomer(string $customerId): CustomerResponse
    {
        // Simulate processing time
        usleep(rand(50000, 150000));

        if (!isset($this->customers[$customerId])) {
            return new CustomerResponse(
                false,
                null,
                null,
                null,
                'CUSTOMER_NOT_FOUND',
                'Customer not found'
            );
        }

        $customer = $this->customers[$customerId];

        return new CustomerResponse(
            true,
            $customerId,
            $customer['email'],
            $customer['name']
        );
    }

    public function deleteCustomer(string $customerId): bool
    {
        // Simulate processing time
        usleep(rand(50000, 150000));

        if (isset($this->customers[$customerId])) {
            unset($this->customers[$customerId]);
            return true;
        }

        return false;
    }

    public function handleWebhook(WebhookRequest $request): WebhookResponse
    {
        // Simulate processing time
        usleep(rand(10000, 50000)); // 10-50ms

        $payload = $request->getParsedPayload();
        
        if (!$payload || !isset($payload['type'])) {
            return new WebhookResponse(
                false,
                null,
                'Invalid webhook payload'
            );
        }

        // Simulate webhook signature verification
        $signature = $request->getSignature();
        if ($signature) {
            $expectedSignature = hash_hmac('sha256', $request->getRawPayload(), 'test_webhook_secret');
            if (!hash_equals($expectedSignature, $signature)) {
                return new WebhookResponse(
                    false,
                    null,
                    'Invalid webhook signature'
                );
            }
        }

        return new WebhookResponse(
            true,
            $payload['type'],
            'Webhook processed successfully',
            $payload['data'] ?? []
        );
    }

    public function getGatewayName(): string
    {
        return 'Mock Gateway';
    }

    public function supportsCurrency(string $currencyCode): bool
    {
        $supportedCurrencies = ['USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY'];
        return in_array(strtoupper($currencyCode), $supportedCurrencies);
    }

    public function supportsCountry(string $countryCode): bool
    {
        $supportedCountries = ['US', 'CA', 'GB', 'AU', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE'];
        return in_array(strtoupper($countryCode), $supportedCountries);
    }

    /**
     * Extract card number from payment request.
     *
     * @param PaymentRequest $request
     * @return string|null
     */
    protected function extractCardNumber(PaymentRequest $request): ?string
    {
        $paymentMethodData = $request->getPaymentMethodData();
        
        if (isset($paymentMethodData['card']['number'])) {
            return $paymentMethodData['card']['number'];
        }

        return null;
    }

    /**
     * Verify webhook signature (mock implementation).
     *
     * @param string $payload
     * @param string $signature
     * @param string $secret
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Test connection to the gateway.
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        // Mock gateway is always available
        return true;
    }

    /**
     * Get gateway capabilities.
     *
     * @return array
     */
    public function getCapabilities(): array
    {
        return [
            'payments' => true,
            'refunds' => true,
            'partial_refunds' => true,
            'customers' => true,
            'webhooks' => true,
            'recurring' => false,
            'marketplace' => false,
        ];
    }

    /**
     * Get supported payment methods.
     *
     * @return array
     */
    public function getSupportedPaymentMethods(): array
    {
        return [
            'card' => [
                'visa',
                'mastercard',
                'amex',
                'discover',
            ],
        ];
    }

    /**
     * Get processing fees structure.
     *
     * @return array
     */
    public function getFeesStructure(): array
    {
        return [
            'card' => [
                'percentage' => 2.9,
                'fixed' => 0.30,
                'currency' => 'USD',
            ],
        ];
    }
}
