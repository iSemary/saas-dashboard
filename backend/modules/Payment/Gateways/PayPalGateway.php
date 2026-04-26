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

class PayPalGateway extends AbstractPaymentGateway
{
    protected string $name = 'paypal';
    protected string $version = 'v2';

    protected array $supportedFeatures = [
        'payments',
        'refunds',
        'partial_refunds',
        'authorization',
        'capture',
        'void',
        'recurring',
        'webhooks',
        'disputes',
        'marketplace',
    ];

    protected array $supportedCurrencies = [
        'USD', 'EUR', 'GBP', 'CAD', 'AUD', 'JPY', 'CHF', 'SEK', 'NOK', 'DKK',
        'PLN', 'CZK', 'HUF', 'BGN', 'RON', 'HRK', 'RUB', 'MXN', 'BRL', 'SGD',
        'HKD', 'NZD', 'KRW', 'MYR', 'THB', 'PHP', 'INR', 'TWD', 'ILS', 'TRY',
    ];

    protected array $supportedCountries = [
        'US', 'CA', 'GB', 'IE', 'AU', 'NZ', 'AT', 'BE', 'BG', 'HR', 'CY', 'CZ',
        'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IT', 'LV', 'LT', 'LU', 'MT',
        'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'CH', 'NO', 'IS', 'BR',
        'MX', 'SG', 'HK', 'JP', 'MY', 'TH', 'PH', 'IN', 'TW', 'IL', 'TR', 'RU',
    ];

    protected ?string $accessToken = null;
    protected ?\DateTime $tokenExpiry = null;

    protected function initialize(): void
    {
        $this->defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function getBaseUrl(): string
    {
        return $this->testMode
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    protected function getAuthHeaders(): array
    {
        $this->ensureValidAccessToken();

        return [
            'Authorization' => 'Bearer ' . $this->accessToken,
        ];
    }

    protected function getPaymentEndpoint(): string
    {
        return 'v2/checkout/orders';
    }

    protected function getRefundEndpoint(): string
    {
        return 'v2/payments/captures/{capture_id}/refund';
    }

    protected function getCaptureEndpoint(string $authorizationId): string
    {
        return "v2/payments/authorizations/{$authorizationId}/capture";
    }

    protected function getVoidEndpoint(string $authorizationId): string
    {
        return "v2/payments/authorizations/{$authorizationId}/void";
    }

    protected function getTestEndpoint(): string
    {
        return 'v1/identity/oauth2/userinfo?schema=paypalv1.1';
    }

    protected function transformPaymentRequest(PaymentRequest $request): array
    {
        $data = [
            'intent' => $request->getCapture() ? 'CAPTURE' : 'AUTHORIZE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $request->getCurrency(),
                        'value' => number_format($request->getAmount(), 2, '.', ''),
                    ],
                ],
            ],
            'payment_source' => $this->buildPaymentSource($request),
        ];

        if ($request->getDescription()) {
            $data['purchase_units'][0]['description'] = $request->getDescription();
        }

        if ($request->getOrderId()) {
            $data['purchase_units'][0]['reference_id'] = $request->getOrderId();
        }

        if ($request->getInvoiceNumber()) {
            $data['purchase_units'][0]['invoice_id'] = $request->getInvoiceNumber();
        }

        if ($request->getBillingAddress() || $request->getShippingAddress()) {
            $data['purchase_units'][0]['shipping'] = $this->buildShippingInfo($request);
        }

        if ($request->getReturnUrl() || $request->getCancelUrl()) {
            $data['application_context'] = [
                'return_url' => $request->getReturnUrl(),
                'cancel_url' => $request->getCancelUrl(),
                'brand_name' => $this->config['brand_name'] ?? null,
                'user_action' => 'PAY_NOW',
            ];
        }

        return $data;
    }

    protected function transformPaymentResponse(array $response): PaymentResponse
    {
        $status = $response['status'] ?? 'FAILED';
        $success = in_array($status, ['COMPLETED', 'APPROVED']);
        $standardStatus = $this->mapPayPalStatusToStandard($status);

        $paymentResponse = new PaymentResponse($success, $standardStatus);
        $paymentResponse->setGatewayTransactionId($response['id'] ?? null)
                       ->setGatewayResponse($response);

        // Extract amount and currency from purchase units
        if (isset($response['purchase_units'][0]['amount'])) {
            $amount = $response['purchase_units'][0]['amount'];
            $paymentResponse->setAmount((float) $amount['value'])
                           ->setCurrency($amount['currency_code']);
        }

        // Extract payment details if available
        if (isset($response['purchase_units'][0]['payments'])) {
            $payments = $response['purchase_units'][0]['payments'];

            if (isset($payments['captures'][0])) {
                $capture = $payments['captures'][0];
                $paymentResponse->setAuthorizationCode($capture['id']);

                if (isset($capture['seller_receivable_breakdown'])) {
                    $paymentResponse->setFees($this->extractPayPalFees($capture['seller_receivable_breakdown']));
                }
            } elseif (isset($payments['authorizations'][0])) {
                $auth = $payments['authorizations'][0];
                $paymentResponse->setAuthorizationCode($auth['id']);
            }
        }

        // Handle redirect URLs for approval
        if (isset($response['links'])) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $paymentResponse->setRedirectUrl($link['href']);
                    break;
                }
            }
        }

        return $paymentResponse;
    }

    protected function transformRefundRequest(RefundRequest $request): array
    {
        $data = [
            'amount' => [
                'value' => number_format($request->getAmount(), 2, '.', ''),
                'currency_code' => 'USD', // Will be overridden by capture currency
            ],
        ];

        if ($request->getReasonDetails()) {
            $data['note_to_payer'] = $request->getReasonDetails();
        }

        if ($request->getRefundId()) {
            $data['invoice_id'] = $request->getRefundId();
        }

        return $data;
    }

    protected function transformRefundResponse(array $response): RefundResponse
    {
        $status = $response['status'] ?? 'FAILED';
        $success = $status === 'COMPLETED';
        $standardStatus = $this->mapPayPalRefundStatusToStandard($status);

        $refundResponse = new RefundResponse($success, $standardStatus);
        $refundResponse->setGatewayRefundId($response['id'] ?? null)
                      ->setGatewayResponse($response);

        if (isset($response['amount'])) {
            $refundResponse->setAmount((float) $response['amount']['value']);
        }

        return $refundResponse;
    }

    protected function transformCaptureRequest(string $authorizationId, float $amount, array $metadata): array
    {
        $data = [
            'amount' => [
                'value' => number_format($amount, 2, '.', ''),
                'currency_code' => 'USD', // Will be determined from authorization
            ],
        ];

        if (isset($metadata['note'])) {
            $data['note_to_payer'] = $metadata['note'];
        }

        if (isset($metadata['invoice_id'])) {
            $data['invoice_id'] = $metadata['invoice_id'];
        }

        return $data;
    }

    protected function transformVoidRequest(string $authorizationId, array $metadata): array
    {
        return []; // PayPal void doesn't require additional data
    }

    public function processPayment(PaymentRequest $request): PaymentResponse
    {
        // First create the order
        $orderResponse = parent::processPayment($request);

        // If the order requires approval (redirect), return the response with redirect URL
        if ($orderResponse->getRedirectUrl()) {
            return $orderResponse;
        }

        // If auto-approved, capture or authorize based on intent
        if ($orderResponse->isSuccess() && $request->getCapture()) {
            return $this->captureOrder($orderResponse->getGatewayTransactionId());
        }

        return $orderResponse;
    }

    protected function captureOrder(string $orderId): PaymentResponse
    {
        try {
            $response = $this->makeHttpRequest('POST', "v2/checkout/orders/{$orderId}/capture", []);
            return $this->transformPaymentResponse($response);
        } catch (\Exception $e) {
            throw new PaymentGatewayException("Order capture failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function createCustomer(CustomerRequest $request): CustomerResponse
    {
        // PayPal doesn't have a direct customer creation API like Stripe
        // We'll simulate this by storing customer data in metadata
        $customerResponse = new CustomerResponse(true);
        $customerResponse->setCustomerId($request->getCustomerId())
                        ->setEmail($request->getEmail())
                        ->setName($request->getName())
                        ->setPhone($request->getPhone());

        return $customerResponse;
    }

    public function updateCustomer(string $customerId, CustomerRequest $request): CustomerResponse
    {
        // PayPal doesn't have customer management, return success
        return $this->createCustomer($request);
    }

    public function deleteCustomer(string $customerId): bool
    {
        // PayPal doesn't have customer management, return success
        return true;
    }

    public function getCustomer(string $customerId): CustomerResponse
    {
        // PayPal doesn't have customer management, return empty response
        return new CustomerResponse(true);
    }

    public function createPaymentMethod(string $customerId, array $paymentMethodData): array
    {
        // PayPal doesn't support stored payment methods in the same way
        throw new PaymentGatewayException(translate('message.operation_failed'));
    }

    public function getPaymentMethods(string $customerId): array
    {
        return [];
    }

    public function deletePaymentMethod(string $paymentMethodId): bool
    {
        return false;
    }

    public function handleWebhook(WebhookRequest $request): WebhookResponse
    {
        try {
            $payload = $request->getParsedPayload();

            if (!$payload) {
                return new WebhookResponse(false, 400);
            }

            $eventType = $payload['event_type'] ?? null;
            $resource = $payload['resource'] ?? null;

            $response = new WebhookResponse(true, 200);
            $response->setEventType($eventType)
                    ->setData($resource);

            // Handle different event types
            switch ($eventType) {
                case 'PAYMENT.CAPTURE.COMPLETED':
                    $response->addAction('update_transaction_status', [
                        'transaction_id' => $resource['supplementary_data']['related_ids']['order_id'] ?? null,
                        'status' => 'completed',
                    ]);
                    break;

                case 'PAYMENT.CAPTURE.DENIED':
                    $response->addAction('update_transaction_status', [
                        'transaction_id' => $resource['supplementary_data']['related_ids']['order_id'] ?? null,
                        'status' => 'failed',
                    ]);
                    break;

                case 'CUSTOMER.DISPUTE.CREATED':
                    $response->addAction('create_chargeback', [
                        'transaction_id' => $resource['disputed_transactions'][0]['seller_transaction_id'] ?? null,
                        'amount' => (float) $resource['dispute_amount']['value'],
                        'reason_code' => $resource['reason'],
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
        // PayPal webhook verification is more complex and requires certificate validation
        // This is a simplified version - in production, use PayPal's SDK
        return true; // Implement proper verification
    }

    public function getTransaction(string $transactionId): array
    {
        return $this->makeHttpRequest('GET', "v2/checkout/orders/{$transactionId}");
    }

    public function getConfigurationRequirements(): array
    {
        return [
            'client_id' => [
                'type' => 'string',
                'required' => true,
                'description' => 'PayPal Client ID',
            ],
            'client_secret' => [
                'type' => 'string',
                'required' => true,
                'secret' => true,
                'description' => 'PayPal Client Secret',
            ],
            'webhook_id' => [
                'type' => 'string',
                'required' => false,
                'description' => 'PayPal Webhook ID',
            ],
            'brand_name' => [
                'type' => 'string',
                'required' => false,
                'description' => 'Brand name displayed to customers',
            ],
        ];
    }

    // Helper methods

    protected function ensureValidAccessToken(): void
    {
        if ($this->accessToken && $this->tokenExpiry && $this->tokenExpiry > new \DateTime()) {
            return;
        }

        $this->refreshAccessToken();
    }

    protected function refreshAccessToken(): void
    {
        $clientId = $this->config['client_id'] ?? '';
        $clientSecret = $this->config['client_secret'] ?? '';

        if (!$clientId || !$clientSecret) {
            throw new PaymentGatewayException("PayPal client credentials not configured");
        }

        $auth = base64_encode($clientId . ':' . $clientSecret);

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $auth,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post($this->getBaseUrl() . '/v1/oauth2/token', [
            'grant_type' => 'client_credentials',
        ]);

        if (!$response->successful()) {
            throw new PaymentGatewayException("Failed to obtain PayPal access token");
        }

        $data = $response->json();
        $this->accessToken = $data['access_token'];
        $this->tokenExpiry = (new \DateTime())->add(new \DateInterval('PT' . $data['expires_in'] . 'S'));
    }

    protected function buildPaymentSource(PaymentRequest $request): array
    {
        $paymentMethodData = $request->getPaymentMethodData();

        if (!$paymentMethodData) {
            // Default to PayPal wallet
            return [
                'paypal' => [
                    'experience_context' => [
                        'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                        'user_action' => 'PAY_NOW',
                    ],
                ],
            ];
        }

        $type = $paymentMethodData['type'] ?? 'paypal';

        switch ($type) {
            case 'card':
                return [
                    'card' => [
                        'number' => $paymentMethodData['card']['number'],
                        'expiry' => $paymentMethodData['card']['exp_month'] . '/' . $paymentMethodData['card']['exp_year'],
                        'security_code' => $paymentMethodData['card']['cvc'],
                        'name' => $paymentMethodData['card']['name'] ?? null,
                    ],
                ];

            default:
                return [
                    'paypal' => [
                        'experience_context' => [
                            'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                            'user_action' => 'PAY_NOW',
                        ],
                    ],
                ];
        }
    }

    protected function buildShippingInfo(PaymentRequest $request): ?array
    {
        $shippingAddress = $request->getShippingAddress();

        if (!$shippingAddress) {
            return null;
        }

        return [
            'address' => [
                'address_line_1' => $shippingAddress['line1'] ?? null,
                'address_line_2' => $shippingAddress['line2'] ?? null,
                'admin_area_2' => $shippingAddress['city'] ?? null,
                'admin_area_1' => $shippingAddress['state'] ?? null,
                'postal_code' => $shippingAddress['postal_code'] ?? null,
                'country_code' => $shippingAddress['country'] ?? null,
            ],
        ];
    }

    protected function mapPayPalStatusToStandard(string $status): string
    {
        $statusMap = [
            'CREATED' => 'pending',
            'SAVED' => 'pending',
            'APPROVED' => 'authorized',
            'VOIDED' => 'cancelled',
            'COMPLETED' => 'completed',
            'PAYER_ACTION_REQUIRED' => 'pending',
        ];

        return $statusMap[$status] ?? 'failed';
    }

    protected function mapPayPalRefundStatusToStandard(string $status): string
    {
        $statusMap = [
            'CANCELLED' => 'cancelled',
            'PENDING' => 'pending',
            'COMPLETED' => 'completed',
        ];

        return $statusMap[$status] ?? 'failed';
    }

    protected function extractPayPalFees(array $breakdown): array
    {
        $fees = [];

        if (isset($breakdown['paypal_fee'])) {
            $fees[] = [
                'type' => 'paypal_fee',
                'amount' => (float) $breakdown['paypal_fee']['value'],
                'currency' => $breakdown['paypal_fee']['currency_code'],
                'description' => 'PayPal processing fee',
            ];
        }

        return $fees;
    }

    /**
     * Create a PayPal order for checkout.
     */
    public function createCheckoutSession(array $options): array
    {
        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $options['currency'] ?? 'USD',
                        'value' => number_format($options['amount'] ?? 0, 2, '.', ''),
                    ],
                    'description' => $options['description'] ?? 'Subscription Payment',
                    'custom_id' => $options['metadata']['invoice_id'] ?? null,
                ],
            ],
            'application_context' => [
                'return_url' => $options['success_url'] ?? config('app.url') . '/billing/checkout/success',
                'cancel_url' => $options['cancel_url'] ?? config('app.url') . '/billing/checkout/cancel',
                'user_action' => 'PAY_NOW',
                'landing_page' => 'BILLING',
            ],
        ];

        return $this->makeHttpRequest('POST', 'v2/checkout/orders', $data);
    }

    /**
     * Create a billing agreement/subscription in PayPal.
     */
    public function createSubscription(array $options): array
    {
        $data = [
            'plan_id' => $options['plan_id'] ?? $this->createBillingPlan($options)['id'],
            'start_time' => $options['start_time'] ?? now()->addMinutes(5)->toIso8601String(),
            'quantity' => $options['quantity'] ?? 1,
            'application_context' => [
                'brand_name' => $this->config['brand_name'] ?? 'SaaS Dashboard',
                'return_url' => $options['success_url'] ?? config('app.url') . '/billing/checkout/success',
                'cancel_url' => $options['cancel_url'] ?? config('app.url') . '/billing/checkout/cancel',
                'user_action' => 'SUBSCRIBE_NOW',
            ],
        ];

        if (isset($options['subscriber'])) {
            $data['subscriber'] = $options['subscriber'];
        }

        return $this->makeHttpRequest('POST', 'v1/billing/subscriptions', $data);
    }

    /**
     * Cancel a PayPal subscription.
     */
    public function cancelSubscription(string $subscriptionId, string $reason = 'Requested by customer'): array
    {
        return $this->makeHttpRequest('POST', "v1/billing/subscriptions/{$subscriptionId}/cancel", [
            'reason' => $reason,
        ]);
    }

    /**
     * Create a billing plan for subscriptions.
     */
    protected function createBillingPlan(array $options): array
    {
        $intervalUnit = match($options['interval'] ?? 'month') {
            'day' => 'DAY',
            'week' => 'WEEK',
            'month' => 'MONTH',
            'year' => 'YEAR',
            default => 'MONTH',
        };

        $data = [
            'product' => [
                'name' => $options['product_name'] ?? 'Subscription',
                'description' => $options['product_description'] ?? 'Recurring subscription',
                'type' => 'DIGITAL',
                'category' => 'SOFTWARE',
            ],
            'billing_cycles' => [
                [
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => number_format($options['amount'] ?? 0, 2, '.', ''),
                            'currency_code' => $options['currency'] ?? 'USD',
                        ],
                    ],
                    'frequency' => [
                        'interval_unit' => $intervalUnit,
                        'interval_count' => 1,
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 0, // Infinite
                ],
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => null,
                'setup_fee_failure_action' => 'CONTINUE',
            ],
        ];

        return $this->makeHttpRequest('POST', 'v1/billing/plans', $data);
    }

    /**
     * Create a setup token for vaulting payment methods.
     */
    public function createSetupIntent(array $options): array
    {
        $data = [
            'payment_source' => [
                'token' => [
                    'type' => 'SETUP_TOKEN',
                    'usage_type' => 'MERCHANT',
                    'customer_type' => 'CONSUMER',
                ],
            ],
        ];

        if (isset($options['customer_id'])) {
            $data['customer'] = [
                'id' => $options['customer_id'],
            ];
        }

        return $this->makeHttpRequest('POST', 'v3/vault/setup-tokens', $data);
    }

    /**
     * Retrieve payment method details.
     */
    public function getPaymentMethod(string $paymentMethodId): array
    {
        // PayPal doesn't store payment methods like Stripe
        // Return vault token info if available
        return [
            'id' => $paymentMethodId,
            'type' => 'paypal',
            'last_four' => null,
            'brand' => 'paypal',
            'exp_month' => null,
            'exp_year' => null,
        ];
    }
}
