<?php

namespace Modules\Payment\Tests\Unit;

use Tests\TestCase;
use Modules\Payment\Services\PaymentGatewayService;
use Modules\Payment\Services\PaymentGatewayFactory;
use Modules\Payment\Services\CurrencyConversionService;
use Modules\Payment\Services\FeeCalculationService;
use Modules\Payment\Services\PaymentRoutingService;
use Modules\Payment\Services\PaymentValidationService;
use Modules\Payment\DTOs\PaymentRequest;
use Modules\Payment\DTOs\PaymentResponse;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Entities\PaymentTransaction;
use Mockery;

class PaymentGatewayServiceTest extends TestCase
{
    protected $paymentGatewayService;
    protected $mockGatewayFactory;
    protected $mockCurrencyService;
    protected $mockFeeService;
    protected $mockRoutingService;
    protected $mockValidationService;
    protected $mockGateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockGatewayFactory = Mockery::mock(PaymentGatewayFactory::class);
        $this->mockCurrencyService = Mockery::mock(CurrencyConversionService::class);
        $this->mockFeeService = Mockery::mock(FeeCalculationService::class);
        $this->mockRoutingService = Mockery::mock(PaymentRoutingService::class);
        $this->mockValidationService = Mockery::mock(PaymentValidationService::class);
        $this->mockGateway = Mockery::mock(PaymentGatewayInterface::class);

        $this->paymentGatewayService = new PaymentGatewayService(
            $this->mockGatewayFactory,
            $this->mockCurrencyService,
            $this->mockFeeService,
            $this->mockRoutingService,
            $this->mockValidationService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_process_payment_success()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $context = ['country' => 'US', 'currency' => 'USD'];

        $paymentMethod = new PaymentMethod([
            'id' => 1,
            'name' => 'Test Gateway',
            'processor_type' => 'stripe',
        ]);

        $expectedResponse = new PaymentResponse(
            true,
            'txn_123',
            'gw_txn_456',
            100.00,
            'USD',
            'completed'
        );

        // Mock routing service to select payment method
        $this->mockRoutingService
            ->shouldReceive('selectOptimalPaymentMethod')
            ->with($paymentRequest, $context)
            ->once()
            ->andReturn($paymentMethod);

        // Mock validation service
        $this->mockValidationService
            ->shouldReceive('validatePaymentRequest')
            ->with($paymentRequest, $paymentMethod)
            ->once()
            ->andReturn([]);

        // Mock fee calculation
        $this->mockFeeService
            ->shouldReceive('calculateFees')
            ->with($paymentRequest, $paymentMethod)
            ->once()
            ->andReturn(['processing_fee' => 2.90]);

        // Mock gateway factory
        $this->mockGatewayFactory
            ->shouldReceive('create')
            ->with($paymentMethod, 'production')
            ->once()
            ->andReturn($this->mockGateway);

        // Mock gateway processing
        $this->mockGateway
            ->shouldReceive('processPayment')
            ->with($paymentRequest)
            ->once()
            ->andReturn($expectedResponse);

        // Act
        $result = $this->paymentGatewayService->processPayment($paymentRequest, $context);

        // Assert
        $this->assertInstanceOf(PaymentResponse::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertEquals('txn_123', $result->getTransactionId());
        $this->assertEquals('completed', $result->getStatus());
    }

    public function test_process_payment_validation_failure()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $context = ['country' => 'US', 'currency' => 'USD'];

        $paymentMethod = new PaymentMethod([
            'id' => 1,
            'name' => 'Test Gateway',
            'processor_type' => 'stripe',
        ]);

        $validationErrors = ['amount' => 'Amount is too low'];

        // Mock routing service
        $this->mockRoutingService
            ->shouldReceive('selectOptimalPaymentMethod')
            ->with($paymentRequest, $context)
            ->once()
            ->andReturn($paymentMethod);

        // Mock validation service to return errors
        $this->mockValidationService
            ->shouldReceive('validatePaymentRequest')
            ->with($paymentRequest, $paymentMethod)
            ->once()
            ->andReturn($validationErrors);

        // Act & Assert
        $this->expectException(\Modules\Payment\Exceptions\PaymentValidationException::class);
        $this->paymentGatewayService->processPayment($paymentRequest, $context);
    }

    public function test_process_payment_gateway_failure()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $context = ['country' => 'US', 'currency' => 'USD'];

        $paymentMethod = new PaymentMethod([
            'id' => 1,
            'name' => 'Test Gateway',
            'processor_type' => 'stripe',
        ]);

        $failedResponse = new PaymentResponse(
            false,
            'txn_123',
            null,
            100.00,
            'USD',
            'failed',
            'CARD_DECLINED',
            'Your card was declined'
        );

        // Setup mocks
        $this->mockRoutingService
            ->shouldReceive('selectOptimalPaymentMethod')
            ->once()
            ->andReturn($paymentMethod);

        $this->mockValidationService
            ->shouldReceive('validatePaymentRequest')
            ->once()
            ->andReturn([]);

        $this->mockFeeService
            ->shouldReceive('calculateFees')
            ->once()
            ->andReturn(['processing_fee' => 2.90]);

        $this->mockGatewayFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($this->mockGateway);

        $this->mockGateway
            ->shouldReceive('processPayment')
            ->once()
            ->andReturn($failedResponse);

        // Act
        $result = $this->paymentGatewayService->processPayment($paymentRequest, $context);

        // Assert
        $this->assertInstanceOf(PaymentResponse::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertEquals('failed', $result->getStatus());
        $this->assertEquals('CARD_DECLINED', $result->getErrorCode());
    }

    public function test_process_payment_with_fallback()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(100.00, 'USD');
        $context = ['country' => 'US', 'currency' => 'USD'];

        $primaryMethod = new PaymentMethod([
            'id' => 1,
            'name' => 'Primary Gateway',
            'processor_type' => 'stripe',
        ]);

        $fallbackMethod = new PaymentMethod([
            'id' => 2,
            'name' => 'Fallback Gateway',
            'processor_type' => 'paypal',
        ]);

        $failedResponse = new PaymentResponse(
            false,
            'txn_123',
            null,
            100.00,
            'USD',
            'failed'
        );

        $successResponse = new PaymentResponse(
            true,
            'txn_124',
            'gw_txn_789',
            100.00,
            'USD',
            'completed'
        );

        // Mock routing service to return primary then fallback
        $this->mockRoutingService
            ->shouldReceive('selectOptimalPaymentMethod')
            ->twice()
            ->andReturn($primaryMethod, $fallbackMethod);

        $this->mockValidationService
            ->shouldReceive('validatePaymentRequest')
            ->twice()
            ->andReturn([]);

        $this->mockFeeService
            ->shouldReceive('calculateFees')
            ->twice()
            ->andReturn(['processing_fee' => 2.90]);

        // Mock gateway factory for both gateways
        $mockPrimaryGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockFallbackGateway = Mockery::mock(PaymentGatewayInterface::class);

        $this->mockGatewayFactory
            ->shouldReceive('create')
            ->with($primaryMethod, 'production')
            ->once()
            ->andReturn($mockPrimaryGateway);

        $this->mockGatewayFactory
            ->shouldReceive('create')
            ->with($fallbackMethod, 'production')
            ->once()
            ->andReturn($mockFallbackGateway);

        // Primary gateway fails
        $mockPrimaryGateway
            ->shouldReceive('processPayment')
            ->once()
            ->andReturn($failedResponse);

        // Fallback gateway succeeds
        $mockFallbackGateway
            ->shouldReceive('processPayment')
            ->once()
            ->andReturn($successResponse);

        // Act
        $result = $this->paymentGatewayService->processPayment($paymentRequest, $context);

        // Assert
        $this->assertInstanceOf(PaymentResponse::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertEquals('txn_124', $result->getTransactionId());
    }

    public function test_currency_conversion_applied()
    {
        // Arrange
        $paymentRequest = new PaymentRequest(100.00, 'EUR');
        $context = ['country' => 'US', 'currency' => 'EUR'];

        $paymentMethod = new PaymentMethod([
            'id' => 1,
            'name' => 'USD Gateway',
            'processor_type' => 'stripe',
            'supported_currencies' => ['USD'],
        ]);

        // Mock currency conversion
        $this->mockCurrencyService
            ->shouldReceive('convert')
            ->with(100.00, 'EUR', 'USD')
            ->once()
            ->andReturn(110.00);

        $this->mockRoutingService
            ->shouldReceive('selectOptimalPaymentMethod')
            ->once()
            ->andReturn($paymentMethod);

        $this->mockValidationService
            ->shouldReceive('validatePaymentRequest')
            ->once()
            ->andReturn([]);

        $this->mockFeeService
            ->shouldReceive('calculateFees')
            ->once()
            ->andReturn(['processing_fee' => 3.20]);

        $this->mockGatewayFactory
            ->shouldReceive('create')
            ->once()
            ->andReturn($this->mockGateway);

        $expectedResponse = new PaymentResponse(
            true,
            'txn_123',
            'gw_txn_456',
            110.00,
            'USD',
            'completed'
        );

        $this->mockGateway
            ->shouldReceive('processPayment')
            ->once()
            ->andReturn($expectedResponse);

        // Act
        $result = $this->paymentGatewayService->processPayment($paymentRequest, $context);

        // Assert
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(110.00, $result->getAmount());
        $this->assertEquals('USD', $result->getCurrency());
    }
}
