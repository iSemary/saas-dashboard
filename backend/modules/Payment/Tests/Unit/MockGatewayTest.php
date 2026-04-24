<?php

namespace Modules\Payment\Tests\Unit;

use Tests\TestCase;
use Modules\Payment\Gateways\MockGateway;
use Modules\Payment\DTOs\PaymentRequest;
use Modules\Payment\DTOs\RefundRequest;
use Modules\Payment\DTOs\CustomerRequest;
use Modules\Payment\DTOs\WebhookRequest;

class MockGatewayTest extends TestCase
{
    protected MockGateway $mockGateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockGateway = new MockGateway(1);
    }

    public function test_successful_payment_processing()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $paymentRequest->setPaymentMethodData([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242', // Stripe test card for success
                'exp_month' => 12,
                'exp_year' => 2025,
                'cvc' => '123'
            ]
        ]);

        // Act
        $response = $this->mockGateway->processPayment($paymentRequest);

        // Assert
        $this->assertTrue($response->isSuccess());
        $this->assertEquals('completed', $response->getStatus());
        $this->assertEquals(100.00, $response->getAmount());
        $this->assertEquals('USD', $response->getCurrency());
        $this->assertNotEmpty($response->getTransactionId());
        $this->assertNotEmpty($response->getGatewayTransactionId());
    }

    public function test_declined_card_payment()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $paymentRequest->setPaymentMethodData([
            'type' => 'card',
            'card' => [
                'number' => '4000000000000002', // Stripe test card for decline
                'exp_month' => 12,
                'exp_year' => 2025,
                'cvc' => '123'
            ]
        ]);

        // Act
        $response = $this->mockGateway->processPayment($paymentRequest);

        // Assert
        $this->assertFalse($response->isSuccess());
        $this->assertEquals('failed', $response->getStatus());
        $this->assertEquals('CARD_DECLINED', $response->getErrorCode());
        $this->assertStringContains('declined', strtolower($response->getErrorMessage()));
    }

    public function test_insufficient_funds_payment()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $paymentRequest->setPaymentMethodData([
            'type' => 'card',
            'card' => [
                'number' => '4000000000009995', // Stripe test card for insufficient funds
                'exp_month' => 12,
                'exp_year' => 2025,
                'cvc' => '123'
            ]
        ]);

        // Act
        $response = $this->mockGateway->processPayment($paymentRequest);

        // Assert
        $this->assertFalse($response->isSuccess());
        $this->assertEquals('failed', $response->getStatus());
        $this->assertEquals('INSUFFICIENT_FUNDS', $response->getErrorCode());
    }

    public function test_expired_card_payment()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $paymentRequest->setPaymentMethodData([
            'type' => 'card',
            'card' => [
                'number' => '4000000000000069', // Stripe test card for expired
                'exp_month' => 12,
                'exp_year' => 2020, // Expired year
                'cvc' => '123'
            ]
        ]);

        // Act
        $response = $this->mockGateway->processPayment($paymentRequest);

        // Assert
        $this->assertFalse($response->isSuccess());
        $this->assertEquals('failed', $response->getStatus());
        $this->assertEquals('EXPIRED_CARD', $response->getErrorCode());
    }

    public function test_successful_refund()
    {
        // Arrange - First process a payment
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $paymentRequest->setPaymentMethodData([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 12,
                'exp_year' => 2025,
                'cvc' => '123'
            ]
        ]);

        $paymentResponse = $this->mockGateway->processPayment($paymentRequest);
        $this->assertTrue($paymentResponse->isSuccess());

        // Now test refund
        $refundRequest = new RefundRequest(
            $paymentResponse->getGatewayTransactionId(),
            50.00
        );

        // Act
        $refundResponse = $this->mockGateway->refund($refundRequest);

        // Assert
        $this->assertTrue($refundResponse->isSuccess());
        $this->assertEquals(50.00, $refundResponse->getAmount());
        $this->assertNotEmpty($refundResponse->getRefundId());
    }

    public function test_refund_exceeds_original_amount()
    {
        // Arrange - First process a payment
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $paymentRequest->setPaymentMethodData([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 12,
                'exp_year' => 2025,
                'cvc' => '123'
            ]
        ]);

        $paymentResponse = $this->mockGateway->processPayment($paymentRequest);

        // Try to refund more than original amount
        $refundRequest = new RefundRequest(
            $paymentResponse->getGatewayTransactionId(),
            150.00 // More than original $100
        );

        // Act
        $refundResponse = $this->mockGateway->refund($refundRequest);

        // Assert
        $this->assertFalse($refundResponse->isSuccess());
        $this->assertEquals('REFUND_AMOUNT_EXCEEDS_ORIGINAL', $refundResponse->getErrorCode());
    }

    public function test_customer_creation()
    {
        // Arrange
        $customerRequest = new CustomerRequest(
            'john.doe@example.com',
            'John Doe'
        );

        // Act
        $customerResponse = $this->mockGateway->createCustomer($customerRequest);

        // Assert
        $this->assertTrue($customerResponse->isSuccess());
        $this->assertNotEmpty($customerResponse->getCustomerId());
        $this->assertEquals('john.doe@example.com', $customerResponse->getEmail());
        $this->assertEquals('John Doe', $customerResponse->getName());
    }

    public function test_webhook_verification()
    {
        // Arrange
        $webhookPayload = json_encode([
            'type' => 'payment.completed',
            'data' => [
                'transaction_id' => 'txn_123',
                'amount' => 100.00,
                'currency' => 'USD'
            ]
        ]);

        $webhookRequest = new WebhookRequest($webhookPayload, [
            'X-Mock-Signature' => hash_hmac('sha256', $webhookPayload, 'test_webhook_secret')
        ]);

        // Act
        $webhookResponse = $this->mockGateway->handleWebhook($webhookRequest);

        // Assert
        $this->assertTrue($webhookResponse->isSuccess());
        $this->assertEquals('payment.completed', $webhookResponse->getEventType());
    }

    public function test_gateway_supports_currency()
    {
        // Test supported currencies
        $this->assertTrue($this->mockGateway->supportsCurrency('USD'));
        $this->assertTrue($this->mockGateway->supportsCurrency('EUR'));
        $this->assertTrue($this->mockGateway->supportsCurrency('GBP'));

        // Test unsupported currency
        $this->assertFalse($this->mockGateway->supportsCurrency('XYZ'));
    }

    public function test_gateway_supports_country()
    {
        // Test supported countries
        $this->assertTrue($this->mockGateway->supportsCountry('US'));
        $this->assertTrue($this->mockGateway->supportsCountry('CA'));
        $this->assertTrue($this->mockGateway->supportsCountry('GB'));

        // Test unsupported country
        $this->assertFalse($this->mockGateway->supportsCountry('XX'));
    }

    public function test_gateway_name()
    {
        $this->assertEquals('Mock Gateway', $this->mockGateway->getGatewayName());
    }

    public function test_processing_time_simulation()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $paymentRequest->setPaymentMethodData([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 12,
                'exp_year' => 2025,
                'cvc' => '123'
            ]
        ]);

        // Act
        $startTime = microtime(true);
        $response = $this->mockGateway->processPayment($paymentRequest);
        $endTime = microtime(true);

        // Assert
        $this->assertTrue($response->isSuccess());
        
        // Mock gateway should simulate some processing time
        $processingTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $this->assertGreaterThan(0, $processingTime);
        $this->assertLessThan(1000, $processingTime); // Should be less than 1 second for mock
    }

    public function test_large_amount_payment()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(50000.00, 'USD'); // Large amount
        $paymentRequest->setPaymentMethodData([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 12,
                'exp_year' => 2025,
                'cvc' => '123'
            ]
        ]);

        // Act
        $response = $this->mockGateway->processPayment($paymentRequest);

        // Assert
        $this->assertTrue($response->isSuccess());
        $this->assertEquals(50000.00, $response->getAmount());
    }

    public function test_multiple_currency_payments()
    {
        $currencies = ['USD', 'EUR', 'GBP', 'CAD'];

        foreach ($currencies as $currency) {
            // Arrange
            $paymentRequest = new PaymentRequest(100.00, $currency);
            $paymentRequest->setPaymentMethodData([
                'type' => 'card',
                'card' => [
                    'number' => '4242424242424242',
                    'exp_month' => 12,
                    'exp_year' => 2025,
                    'cvc' => '123'
                ]
            ]);

            // Act
            $response = $this->mockGateway->processPayment($paymentRequest);

            // Assert
            $this->assertTrue($response->isSuccess(), "Payment failed for currency: {$currency}");
            $this->assertEquals($currency, $response->getCurrency());
        }
    }
}
