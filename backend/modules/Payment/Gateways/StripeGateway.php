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
use Modules\Payment\Exceptions\PaymentGatewayException;

class StripeGateway extends AbstractPaymentGateway
{
    protected string $name = 'stripe';
    protected string $version = '2023-10-16';

    protected array $supportedFeatures = [
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

    protected array $supportedCurrencies = [
        'USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CHF', 'SEK', 'NOK', 'DKK',
        'PLN', 'CZK', 'HUF', 'BGN', 'RON', 'HRK', 'ISK', 'MXN', 'BRL', 'SGD',
        'HKD', 'NZD', 'KRW', 'MYR', 'THB', 'PHP', 'INR', 'IDR', 'VND', 'AED',
        'SAR', 'QAR', 'KWD', 'BHD', 'OMR', 'JOD', 'LBP', 'EGP', 'MAD', 'TND',
        'DZD', 'LYD', 'ZAR', 'KES', 'UGX', 'TZS', 'GHS', 'NGN', 'XOF', 'XAF',
    ];

    protected array $supportedCountries = [
        'US', 'CA', 'GB', 'IE', 'AU', 'NZ', 'AT', 'BE', 'BG', 'HR', 'CY', 'CZ',
        'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IT', 'LV', 'LT', 'LU', 'MT',
        'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'CH', 'NO', 'IS', 'LI',
        'BR', 'MX', 'SG', 'HK', 'JP', 'MY', 'TH', 'PH', 'IN', 'ID', 'AE', 'SA',
    ];

    protected function initialize(): void
    {
        $this->defaultHeaders = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Stripe-Version' => $this->version,
        ];
    }

    protected function getBaseUrl(): string
    {
        return 'https://api.stripe.com/v1';
    }

    protected function getAuthHeaders(): array
    {
        $apiKey = $this->testMode
            ? $this->config['test_secret_key'] ?? ''
            : $this->config['live_secret_key'] ?? '';

        return [
            'Authorization' => 'Bearer ' . $apiKey,
        ];
    }

    protected function getPaymentEndpoint(): string
    {
        return 'payment_intents';
    }

    protected function getRefundEndpoint(): string
    {
        return 'refunds';
    }

    protected function getCaptureEndpoint(string $authorizationId): string
    {
        return "payment_intents/{$authorizationId}/capture";
    }

    protected function getVoidEndpoint(string $authorizationId): string
    {
        return "payment_intents/{$authorizationId}/cancel";
    }

    protected function getTestEndpoint(): string
    {
        return 'account';
    }

    protected function transformPaymentRequest(PaymentRequest $request): array
    {
        $data = [
            'amount' => $this->convertAmountToMinorUnits($request->getAmount(), $request->getCurrency()),
            'currency' => strtolower($request->getCurrency()),
            'capture_method' => $request->getCapture() ? 'automatic' : 'manual',
            'confirmation_method' => 'automatic',
            'confirm' => true,
        ];

        if ($request->getDescription()) {
            $data['description'] = $request->getDescription();
        }

        if ($request->getStatementDescriptor()) {
            $data['statement_descriptor'] = $request->getStatementDescriptor();
        }

        if ($request->getReceiptEmail()) {
            $data['receipt_email'] = $request->getReceiptEmail();
        }

        if ($request->getCustomerId()) {
            $data['customer'] = $request->getCustomerId();
        }

        if ($request->getPaymentMethodId()) {
            $data['payment_method'] = $request->getPaymentMethodId();
        } elseif ($request->getPaymentMethodData()) {
            $data['payment_method_data'] = $this->transformPaymentMethodData($request->getPaymentMethodData());
        }

        if ($request->getBillingAddress()) {
            $data['billing_details'] = [
                'address' => $this->transformAddress($request->getBillingAddress()),
            ];
        }

        if ($request->getMetadata()) {
            $data['metadata'] = $request->getMetadata();
        }

        if ($request->getReturnUrl()) {
            $data['return_url'] = $request->getReturnUrl();
        }

        return $data;
    }

    protected function transformPaymentResponse(array $response): PaymentResponse
    {
        $success = in_array($response['status'] ?? '', ['succeeded', 'requires_capture']);
        $status = $this->mapStripeStatusToStandard($response['status'] ?? 'failed');

        $paymentResponse = new PaymentResponse($success, $status);
        $paymentResponse->setGatewayTransactionId($response['id'] ?? null)
                       ->setAmount($this->convertAmountFromMinorUnits($response['amount'] ?? 0, $response['currency'] ?? 'usd'))
                       ->setCurrency(strtoupper($response['currency'] ?? ''))
                       ->setGatewayResponse($response);

        if (isset($response['charges']['data'][0])) {
            $charge = $response['charges']['data'][0];
            $paymentResponse->setAuthorizationCode($charge['authorization_code'] ?? null);

            if (isset($charge['outcome'])) {
                $paymentResponse->setAvsResult($charge['outcome']['seller_message'] ?? null)
                               ->setCvvResult($charge['outcome']['type'] ?? null);
            }

            if (isset($charge['balance_transaction'])) {
                $paymentResponse->setFees($this->extractFees($charge['balance_transaction']));
            }
        }

        if (!$success && isset($response['last_payment_error'])) {
            $error = $response['last_payment_error'];
            $paymentResponse->setErrorCode($error['code'] ?? null)
                           ->setErrorMessage($error['message'] ?? null);
        }

        return $paymentResponse;
    }

    protected function transformRefundRequest(RefundRequest $request): array
    {
        $data = [
            'payment_intent' => $request->getTransactionId(),
            'amount' => $this->convertAmountToMinorUnits($request->getAmount(), 'usd'), // Currency will be determined by PI
        ];

        if ($request->getReason()) {
            $data['reason'] = $this->mapReasonToStripe($request->getReason());
        }

        if ($request->getMetadata()) {
            $data['metadata'] = $request->getMetadata();
        }

        if ($request->getRefundId()) {
            $data['metadata']['internal_refund_id'] = $request->getRefundId();
        }

        return $data;
    }

    protected function transformRefundResponse(array $response): RefundResponse
    {
        $success = $response['status'] === 'succeeded';
        $status = $this->mapStripeRefundStatusToStandard($response['status'] ?? 'failed');

        $refundResponse = new RefundResponse($success, $status);
        $refundResponse->setGatewayRefundId($response['id'] ?? null)
                      ->setAmount($this->convertAmountFromMinorUnits($response['amount'] ?? 0, $response['currency'] ?? 'usd'))
                      ->setGatewayResponse($response);

        if (!$success && isset($response['failure_reason'])) {
            $refundResponse->setErrorMessage($response['failure_reason']);
        }

        return $refundResponse;
    }

    protected function transformCaptureRequest(string $authorizationId, float $amount, array $metadata): array
    {
        $data = [];

        if ($amount > 0) {
            $data['amount_to_capture'] = $this->convertAmountToMinorUnits($amount, 'usd'); // Currency from PI
        }

        if (!empty($metadata)) {
            $data['metadata'] = $metadata;
        }

        return $data;
    }

    protected function transformVoidRequest(string $authorizationId, array $metadata): array
    {
        $data = [];

        if (!empty($metadata)) {
            $data['metadata'] = $metadata;
        }

        return $data;
    }

    public function createCustomer(CustomerRequest $request): CustomerResponse
    {
        try {
            $data = [];

            if ($request->getEmail()) {
                $data['email'] = $request->getEmail();
            }

            if ($request->getName()) {
                $data['name'] = $request->getName();
            }

            if ($request->getPhone()) {
                $data['phone'] = $request->getPhone();
            }

            if ($request->getDescription()) {
                $data['description'] = $request->getDescription();
            }

            if ($request->getAddress()) {
                $data['address'] = $this->transformAddress($request->getAddress());
            }

            if ($request->getShipping()) {
                $data['shipping'] = [
                    'address' => $this->transformAddress($request->getShipping()),
                    'name' => $request->getName(),
                ];
            }

            if ($request->getMetadata()) {
                $data['metadata'] = $request->getMetadata();
            }

            $response = $this->makeHttpRequest('POST', 'customers', $data);

            $customerResponse = new CustomerResponse(true);
            $customerResponse->setGatewayCustomerId($response['id'])
                            ->setEmail($response['email'] ?? null)
                            ->setName($response['name'] ?? null)
                            ->setPhone($response['phone'] ?? null)
                            ->setGatewayResponse($response);

            return $customerResponse;

        } catch (\Exception $e) {
            $customerResponse = new CustomerResponse(false);
            $customerResponse->setErrorMessage($e->getMessage());
            return $customerResponse;
        }
    }

    public function updateCustomer(string $customerId, CustomerRequest $request): CustomerResponse
    {
        try {
            $data = [];

            if ($request->getEmail()) {
                $data['email'] = $request->getEmail();
            }

            if ($request->getName()) {
                $data['name'] = $request->getName();
            }

            if ($request->getPhone()) {
                $data['phone'] = $request->getPhone();
            }

            if ($request->getDescription()) {
                $data['description'] = $request->getDescription();
            }

            if ($request->getAddress()) {
                $data['address'] = $this->transformAddress($request->getAddress());
            }

            if ($request->getMetadata()) {
                $data['metadata'] = $request->getMetadata();
            }

            $response = $this->makeHttpRequest('POST', "customers/{$customerId}", $data);

            $customerResponse = new CustomerResponse(true);
            $customerResponse->setGatewayCustomerId($response['id'])
                            ->setEmail($response['email'] ?? null)
                            ->setName($response['name'] ?? null)
                            ->setPhone($response['phone'] ?? null)
                            ->setGatewayResponse($response);

            return $customerResponse;

        } catch (\Exception $e) {
            $customerResponse = new CustomerResponse(false);
            $customerResponse->setErrorMessage($e->getMessage());
            return $customerResponse;
        }
    }

    public function deleteCustomer(string $customerId): bool
    {
        try {
            $this->makeHttpRequest('DELETE', "customers/{$customerId}");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getCustomer(string $customerId): CustomerResponse
    {
        try {
            $response = $this->makeHttpRequest('GET', "customers/{$customerId}");

            $customerResponse = new CustomerResponse(true);
            $customerResponse->setGatewayCustomerId($response['id'])
                            ->setEmail($response['email'] ?? null)
                            ->setName($response['name'] ?? null)
                            ->setPhone($response['phone'] ?? null)
                            ->setGatewayResponse($response);

            return $customerResponse;

        } catch (\Exception $e) {
            $customerResponse = new CustomerResponse(false);
            $customerResponse->setErrorMessage($e->getMessage());
            return $customerResponse;
        }
    }

    public function createPaymentMethod(string $customerId, array $paymentMethodData): array
    {
        $data = [
            'type' => $paymentMethodData['type'] ?? 'card',
        ];

        if (isset($paymentMethodData['card'])) {
            $data['card'] = $paymentMethodData['card'];
        }

        if (isset($paymentMethodData['billing_details'])) {
            $data['billing_details'] = $paymentMethodData['billing_details'];
        }

        $response = $this->makeHttpRequest('POST', 'payment_methods', $data);

        // Attach to customer
        $this->makeHttpRequest('POST', "payment_methods/{$response['id']}/attach", [
            'customer' => $customerId,
        ]);

        return $response;
    }

    public function getPaymentMethods(string $customerId): array
    {
        $response = $this->makeHttpRequest('GET', 'payment_methods', [
            'customer' => $customerId,
            'type' => 'card',
        ]);

        return $response['data'] ?? [];
    }

    public function deletePaymentMethod(string $paymentMethodId): bool
    {
        try {
            $this->makeHttpRequest('POST', "payment_methods/{$paymentMethodId}/detach");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function handleWebhook(WebhookRequest $request): WebhookResponse
    {
        try {
            $payload = $request->getParsedPayload();

            if (!$payload) {
                return new WebhookResponse(false, 400);
            }

            $eventType = $payload['type'] ?? null;
            $eventData = $payload['data']['object'] ?? null;

            $response = new WebhookResponse(true, 200);
            $response->setEventType($eventType)
                    ->setData($eventData);

            // Handle different event types
            switch ($eventType) {
                case 'payment_intent.succeeded':
                    $response->addAction('update_transaction_status', [
                        'transaction_id' => $eventData['id'],
                        'status' => 'completed',
                    ]);
                    break;

                case 'payment_intent.payment_failed':
                    $response->addAction('update_transaction_status', [
                        'transaction_id' => $eventData['id'],
                        'status' => 'failed',
                    ]);
                    break;

                case 'charge.dispute.created':
                    $response->addAction('create_chargeback', [
                        'transaction_id' => $eventData['payment_intent'],
                        'amount' => $this->convertAmountFromMinorUnits($eventData['amount'], $eventData['currency']),
                        'reason_code' => $eventData['reason'],
                    ]);
                    break;

                case 'invoice.payment_succeeded':
                    $response->addAction('update_subscription_status', [
                        'subscription_id' => $eventData['subscription'],
                        'status' => 'active',
                    ]);
                    break;
            }

            return $response;

        } catch (\Exception $e) {
            $response = new WebhookResponse(false, 500);
            $response->setMessage($e->getMessage());
            return $response;
        }
    }

    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    public function getTransaction(string $transactionId): array
    {
        return $this->makeHttpRequest('GET', "payment_intents/{$transactionId}");
    }

    /**
     * Create a SetupIntent for adding a payment method.
     */
    public function createSetupIntent(array $options): array
    {
        $data = [
            'usage' => 'off_session',
        ];

        if (isset($options['customer_id'])) {
            $data['customer'] = $options['customer_id'];
        }

        if (isset($options['metadata'])) {
            $data['metadata'] = $options['metadata'];
        }

        return $this->makeHttpRequest('POST', 'setup_intents', $data);
    }

    /**
     * Create a Checkout Session for subscription payment.
     */
    public function createCheckoutSession(array $options): array
    {
        $data = [
            'mode' => 'subscription',
            'success_url' => $options['success_url'] ?? config('app.url') . '/billing/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $options['cancel_url'] ?? config('app.url') . '/billing/checkout/cancel',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower($options['currency'] ?? 'usd'),
                        'product_data' => [
                            'name' => $options['description'] ?? 'Subscription',
                        ],
                        'unit_amount' => $this->convertAmountToMinorUnits($options['amount'] ?? 0, $options['currency'] ?? 'usd'),
                        'recurring' => [
                            'interval' => $options['interval'] ?? 'month',
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
        ];

        if (isset($options['customer_id'])) {
            $data['customer'] = $options['customer_id'];
        } else {
            $data['customer_email'] = $options['email'] ?? null;
        }

        if (isset($options['metadata'])) {
            $data['metadata'] = $options['metadata'];
        }

        if (isset($options['subscription_data'])) {
            $data['subscription_data'] = $options['subscription_data'];
        }

        return $this->makeHttpRequest('POST', 'checkout/sessions', $data);
    }

    /**
     * Create a subscription in Stripe.
     */
    public function createSubscription(array $options): array
    {
        $data = [
            'customer' => $options['customer_id'],
            'items' => [
                [
                    'price' => $options['price_id'],
                    'quantity' => $options['quantity'] ?? 1,
                ],
            ],
        ];

        if (isset($options['trial_end'])) {
            $data['trial_end'] = $options['trial_end'];
        }

        if (isset($options['default_payment_method'])) {
            $data['default_payment_method'] = $options['default_payment_method'];
        }

        if (isset($options['metadata'])) {
            $data['metadata'] = $options['metadata'];
        }

        return $this->makeHttpRequest('POST', 'subscriptions', $data);
    }

    /**
     * Cancel a subscription in Stripe.
     */
    public function cancelSubscription(string $subscriptionId, bool $atPeriodEnd = true): array
    {
        if ($atPeriodEnd) {
            return $this->makeHttpRequest('POST', "subscriptions/{$subscriptionId}", [
                'cancel_at_period_end' => true,
            ]);
        }

        return $this->makeHttpRequest('DELETE', "subscriptions/{$subscriptionId}");
    }

    /**
     * Retrieve a payment method from Stripe.
     */
    public function getPaymentMethod(string $paymentMethodId): array
    {
        $response = $this->makeHttpRequest('GET', "payment_methods/{$paymentMethodId}");

        return [
            'id' => $response['id'],
            'type' => $response['type'],
            'last_four' => $response['card']['last4'] ?? null,
            'brand' => $response['card']['brand'] ?? null,
            'exp_month' => $response['card']['exp_month'] ?? null,
            'exp_year' => $response['card']['exp_year'] ?? null,
        ];
    }

    public function getConfigurationRequirements(): array
    {
        return [
            'live_publishable_key' => [
                'type' => 'string',
                'required' => true,
                'description' => 'Stripe live publishable key',
            ],
            'live_secret_key' => [
                'type' => 'string',
                'required' => true,
                'secret' => true,
                'description' => 'Stripe live secret key',
            ],
            'test_publishable_key' => [
                'type' => 'string',
                'required' => false,
                'description' => 'Stripe test publishable key',
            ],
            'test_secret_key' => [
                'type' => 'string',
                'required' => false,
                'secret' => true,
                'description' => 'Stripe test secret key',
            ],
            'webhook_secret' => [
                'type' => 'string',
                'required' => false,
                'secret' => true,
                'description' => 'Stripe webhook endpoint secret',
            ],
        ];
    }

    // Helper methods

    protected function convertAmountToMinorUnits(float $amount, string $currency): int
    {
        $zeroDecimalCurrencies = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];

        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return (int) $amount;
        }

        return (int) ($amount * 100);
    }

    protected function convertAmountFromMinorUnits(int $amount, string $currency): float
    {
        $zeroDecimalCurrencies = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];

        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return (float) $amount;
        }

        return $amount / 100;
    }

    protected function transformAddress(array $address): array
    {
        return [
            'line1' => $address['line1'] ?? null,
            'line2' => $address['line2'] ?? null,
            'city' => $address['city'] ?? null,
            'state' => $address['state'] ?? null,
            'postal_code' => $address['postal_code'] ?? null,
            'country' => $address['country'] ?? null,
        ];
    }

    protected function transformPaymentMethodData(array $data): array
    {
        return [
            'type' => $data['type'] ?? 'card',
            'card' => $data['card'] ?? [],
            'billing_details' => $data['billing_details'] ?? [],
        ];
    }

    protected function mapStripeStatusToStandard(string $status): string
    {
        $statusMap = [
            'requires_payment_method' => 'pending',
            'requires_confirmation' => 'pending',
            'requires_action' => 'pending',
            'processing' => 'processing',
            'requires_capture' => 'authorized',
            'succeeded' => 'completed',
            'canceled' => 'cancelled',
        ];

        return $statusMap[$status] ?? 'failed';
    }

    protected function mapStripeRefundStatusToStandard(string $status): string
    {
        $statusMap = [
            'pending' => 'pending',
            'succeeded' => 'completed',
            'failed' => 'failed',
            'canceled' => 'cancelled',
        ];

        return $statusMap[$status] ?? 'failed';
    }

    protected function mapReasonToStripe(string $reason): string
    {
        $reasonMap = [
            'requested_by_customer' => 'requested_by_customer',
            'duplicate' => 'duplicate',
            'fraudulent' => 'fraudulent',
            'subscription_cancellation' => 'requested_by_customer',
            'other' => 'requested_by_customer',
        ];

        return $reasonMap[$reason] ?? 'requested_by_customer';
    }

    protected function extractFees(string $balanceTransactionId): array
    {
        try {
            $bt = $this->makeHttpRequest('GET', "balance_transactions/{$balanceTransactionId}");

            $fees = [];
            foreach ($bt['fee_details'] ?? [] as $fee) {
                $fees[] = [
                    'type' => $fee['type'],
                    'amount' => $this->convertAmountFromMinorUnits($fee['amount'], $bt['currency']),
                    'currency' => strtoupper($bt['currency']),
                    'description' => $fee['description'] ?? null,
                ];
            }

            return $fees;
        } catch (\Exception $e) {
            return [];
        }
    }
}
