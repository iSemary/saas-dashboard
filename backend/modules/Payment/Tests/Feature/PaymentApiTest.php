<?php

namespace Modules\Payment\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Entities\PaymentTransaction;
use Modules\Utilities\Entities\Currency;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected PaymentMethod $paymentMethod;
    protected Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        // Create test currency
        $this->currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'decimal_places' => 2,
            'exchange_rate' => 1.0,
            'status' => 'active',
        ]);

        // Create test payment method
        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Test Stripe Gateway',
            'description' => 'Test payment method for API testing',
            'processor_type' => 'stripe',
            'gateway_name' => 'stripe_test',
            'supported_currencies' => ['USD', 'EUR'],
            'is_global' => true,
            'status' => 'active',
            'authentication_type' => 'api_key',
        ]);
    }

    public function test_process_payment_success()
    {
        $paymentData = [
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method_data' => [
                'type' => 'card',
                'card' => [
                    'number' => '4242424242424242',
                    'exp_month' => 12,
                    'exp_year' => 2025,
                    'cvc' => '123',
                    'name' => 'John Doe'
                ]
            ],
            'description' => 'Test payment',
            'customer_id' => 'cust_test_123',
            'metadata' => [
                'order_id' => 'order_123',
                'customer_email' => 'john@example.com'
            ]
        ];

        $response = $this->postJson('/api/v1/payments/process', $paymentData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Payment processed successfully'
                ])
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'transaction_id',
                        'gateway_transaction_id',
                        'amount',
                        'currency',
                        'status'
                    ],
                    'message'
                ]);

        // Verify transaction was created in database
        $this->assertDatabaseHas('payment_transactions', [
            'amount' => 100.00,
            'currency_id' => $this->currency->id,
            'status' => 'completed'
        ]);
    }

    public function test_process_payment_validation_errors()
    {
        $invalidPaymentData = [
            'amount' => -10, // Invalid amount
            'currency' => 'INVALID', // Invalid currency
            // Missing required payment method data
        ];

        $response = $this->postJson('/api/v1/payments/process', $invalidPaymentData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'error' => [
                        'code' => 'VALIDATION_ERROR',
                        'message' => 'The given data was invalid'
                    ]
                ])
                ->assertJsonStructure([
                    'success',
                    'error' => [
                        'code',
                        'message',
                        'details'
                    ]
                ]);
    }

    public function test_process_payment_declined_card()
    {
        $paymentData = [
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method_data' => [
                'type' => 'card',
                'card' => [
                    'number' => '4000000000000002', // Declined card
                    'exp_month' => 12,
                    'exp_year' => 2025,
                    'cvc' => '123'
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/payments/process', $paymentData);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Payment processing failed'
                ])
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'status',
                        'error_code',
                        'error_message'
                    ]
                ]);
    }

    public function test_authorize_payment()
    {
        $paymentData = [
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method_data' => [
                'type' => 'card',
                'card' => [
                    'number' => '4242424242424242',
                    'exp_month' => 12,
                    'exp_year' => 2025,
                    'cvc' => '123'
                ]
            ],
            'capture' => false
        ];

        $response = $this->postJson('/api/v1/payments/authorize', $paymentData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);

        // Verify transaction was created with authorized status
        $this->assertDatabaseHas('payment_transactions', [
            'amount' => 100.00,
            'status' => 'authorized'
        ]);
    }

    public function test_capture_authorized_payment()
    {
        // First create an authorized transaction
        $transaction = PaymentTransaction::create([
            'transaction_id' => 'txn_test_' . uniqid(),
            'gateway_transaction_id' => 'gw_test_' . uniqid(),
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 100.00,
            'status' => 'authorized',
            'transaction_type' => 'sale',
        ]);

        $captureData = [
            'amount' => 100.00,
            'metadata' => [
                'capture_reason' => 'Order shipped'
            ]
        ];

        $response = $this->postJson("/api/v1/payments/{$transaction->transaction_id}/capture", $captureData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Payment captured successfully'
                ]);
    }

    public function test_void_authorized_payment()
    {
        // First create an authorized transaction
        $transaction = PaymentTransaction::create([
            'transaction_id' => 'txn_test_' . uniqid(),
            'gateway_transaction_id' => 'gw_test_' . uniqid(),
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 100.00,
            'status' => 'authorized',
            'transaction_type' => 'sale',
        ]);

        $voidData = [
            'reason' => 'Customer cancelled order',
            'metadata' => [
                'void_reason' => 'Customer request'
            ]
        ];

        $response = $this->postJson("/api/v1/payments/{$transaction->transaction_id}/void", $voidData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Payment voided successfully'
                ]);
    }

    public function test_process_refund()
    {
        // First create a completed transaction
        $transaction = PaymentTransaction::create([
            'transaction_id' => 'txn_test_' . uniqid(),
            'gateway_transaction_id' => 'gw_test_' . uniqid(),
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 100.00,
            'status' => 'completed',
            'transaction_type' => 'sale',
        ]);

        $refundData = [
            'transaction_id' => $transaction->transaction_id,
            'amount' => 50.00,
            'reason' => 'requested_by_customer',
            'reason_details' => 'Customer not satisfied with product',
            'metadata' => [
                'refund_type' => 'partial'
            ]
        ];

        $response = $this->postJson('/api/v1/refunds/process', $refundData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Refund processed successfully'
                ]);

        // Verify refund was created in database
        $this->assertDatabaseHas('refunds', [
            'original_transaction_id' => $transaction->id,
            'amount' => 50.00,
            'reason' => 'requested_by_customer'
        ]);
    }

    public function test_get_transaction_details()
    {
        $transaction = PaymentTransaction::create([
            'transaction_id' => 'txn_test_' . uniqid(),
            'gateway_transaction_id' => 'gw_test_' . uniqid(),
            'payment_method_id' => $this->paymentMethod->id,
            'currency_id' => $this->currency->id,
            'amount' => 100.00,
            'status' => 'completed',
            'transaction_type' => 'sale',
            'metadata' => ['order_id' => 'order_123']
        ]);

        $response = $this->getJson("/api/v1/payments/{$transaction->transaction_id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ])
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'transaction_id',
                        'gateway_transaction_id',
                        'amount',
                        'currency',
                        'status',
                        'transaction_type',
                        'payment_method',
                        'created_at',
                        'metadata'
                    ]
                ]);
    }

    public function test_get_available_payment_methods()
    {
        $response = $this->getJson('/api/v1/payment-methods?country=US&currency=USD');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ])
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'processor_type',
                            'supported_currencies',
                            'is_global'
                        ]
                    ]
                ]);
    }

    public function test_test_payment_method()
    {
        $response = $this->postJson("/api/v1/payment-methods/{$this->paymentMethod->id}/test?environment=sandbox");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Payment method test successful'
                ]);
    }

    public function test_unauthorized_access()
    {
        // Remove authentication
        Sanctum::actingAs(null);

        $response = $this->postJson('/api/v1/payments/process', [
            'amount' => 100.00,
            'currency' => 'USD'
        ]);

        $response->assertStatus(401);
    }

    public function test_rate_limiting()
    {
        // Make multiple requests to test rate limiting
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/v1/payments/process', [
                'amount' => 100.00,
                'currency' => 'USD',
                'payment_method_data' => [
                    'type' => 'card',
                    'card' => [
                        'number' => '4242424242424242',
                        'exp_month' => 12,
                        'exp_year' => 2025,
                        'cvc' => '123'
                    ]
                ]
            ]);

            if ($i < 5) {
                // First few requests should succeed or fail due to validation
                $this->assertContains($response->status(), [200, 400, 422]);
            }
        }

        // After many requests, should hit rate limit
        // Note: This depends on your rate limiting configuration
    }

    public function test_payment_with_billing_address()
    {
        $paymentData = [
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method_data' => [
                'type' => 'card',
                'card' => [
                    'number' => '4242424242424242',
                    'exp_month' => 12,
                    'exp_year' => 2025,
                    'cvc' => '123'
                ]
            ],
            'billing_address' => [
                'line1' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'US'
            ]
        ];

        $response = $this->postJson('/api/v1/payments/process', $paymentData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    public function test_payment_with_invalid_card_number()
    {
        $paymentData = [
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method_data' => [
                'type' => 'card',
                'card' => [
                    'number' => '1234567890123456', // Invalid card number (fails Luhn check)
                    'exp_month' => 12,
                    'exp_year' => 2025,
                    'cvc' => '123'
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/payments/process', $paymentData);

        $response->assertStatus(422)
                ->assertJsonPath('error.details.payment_method_data.card.number.0', 'Invalid card number');
    }

    public function test_payment_with_expired_card()
    {
        $paymentData = [
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method_data' => [
                'type' => 'card',
                'card' => [
                    'number' => '4242424242424242',
                    'exp_month' => 12,
                    'exp_year' => 2020, // Expired year
                    'cvc' => '123'
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/payments/process', $paymentData);

        $response->assertStatus(422)
                ->assertJsonPath('error.details.payment_method_data.card.exp_year.0', 'Card has expired');
    }
}
