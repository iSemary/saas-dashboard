<?php

namespace Modules\Payment\Services;

use Modules\Payment\DTOs\PaymentRequest;
use Modules\Payment\DTOs\PaymentResponse;
use Modules\Payment\DTOs\RefundRequest;
use Modules\Payment\DTOs\RefundResponse;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Entities\PaymentTransaction;
use Modules\Payment\Entities\Refund;
use Modules\Payment\Exceptions\PaymentGatewayException;
use Modules\Utilities\Entities\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    protected PaymentGatewayFactory $gatewayFactory;
    protected PaymentRoutingService $routingService;
    protected FeeCalculationService $feeCalculationService;
    protected CurrencyConversionService $currencyConversionService;
    protected PaymentValidationService $validationService;

    public function __construct(
        PaymentGatewayFactory $gatewayFactory,
        PaymentRoutingService $routingService,
        FeeCalculationService $feeCalculationService,
        CurrencyConversionService $currencyConversionService,
        PaymentValidationService $validationService
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->routingService = $routingService;
        $this->feeCalculationService = $feeCalculationService;
        $this->currencyConversionService = $currencyConversionService;
        $this->validationService = $validationService;
    }

    /**
     * Process a payment transaction.
     *
     * @param PaymentRequest $request
     * @param array $context
     * @return PaymentResponse
     * @throws PaymentGatewayException
     */
    public function processPayment(PaymentRequest $request, array $context = []): PaymentResponse
    {
        return DB::transaction(function () use ($request, $context) {
            try {
                // Validate the payment request
                $this->validationService->validatePaymentRequest($request, $context);

                // Determine the best payment method using routing
                $paymentMethod = $this->routingService->selectPaymentMethod($request, $context);
                
                if (!$paymentMethod) {
                    throw new PaymentGatewayException("No suitable payment method found");
                }

                // Calculate fees
                $feeBreakdown = $this->feeCalculationService->calculateFees(
                    $paymentMethod,
                    $request->getAmount(),
                    $request->getCurrency(),
                    $context
                );

                // Convert currency if needed
                $conversionResult = $this->currencyConversionService->convert(
                    $request->getAmount(),
                    $request->getCurrency(),
                    $this->getBaseCurrency()
                );

                // Create transaction record
                $transaction = $this->createTransactionRecord($request, $paymentMethod, $feeBreakdown, $conversionResult, $context);

                // Process payment through gateway
                $gateway = $this->gatewayFactory->create($paymentMethod, $this->getEnvironment());
                $response = $gateway->processPayment($request);

                // Update transaction with gateway response
                $this->updateTransactionFromResponse($transaction, $response);

                // Update routing rule statistics
                $this->routingService->recordTransactionResult($paymentMethod, $response->isSuccess());

                return $response;

            } catch (\Exception $e) {
                Log::error('Payment processing failed', [
                    'error' => $e->getMessage(),
                    'request' => $request->toArray(),
                    'context' => $context,
                ]);

                if (isset($transaction)) {
                    $this->markTransactionFailed($transaction, $e->getMessage());
                }

                throw $e;
            }
        });
    }

    /**
     * Process a refund.
     *
     * @param RefundRequest $request
     * @param array $context
     * @return RefundResponse
     * @throws PaymentGatewayException
     */
    public function processRefund(RefundRequest $request, array $context = []): RefundResponse
    {
        return DB::transaction(function () use ($request, $context) {
            try {
                // Find the original transaction
                $originalTransaction = PaymentTransaction::where('transaction_id', $request->getTransactionId())
                                                       ->orWhere('gateway_transaction_id', $request->getTransactionId())
                                                       ->first();

                if (!$originalTransaction) {
                    throw new PaymentGatewayException("Original transaction not found");
                }

                // Validate refund request
                $this->validationService->validateRefundRequest($request, $originalTransaction, $context);

                // Create refund record
                $refund = $this->createRefundRecord($request, $originalTransaction, $context);

                // Process refund through gateway
                $gateway = $this->gatewayFactory->create($originalTransaction->paymentMethod, $this->getEnvironment());
                $response = $gateway->processRefund($request);

                // Update refund with gateway response
                $this->updateRefundFromResponse($refund, $response);

                return $response;

            } catch (\Exception $e) {
                Log::error('Refund processing failed', [
                    'error' => $e->getMessage(),
                    'request' => $request->toArray(),
                    'context' => $context,
                ]);

                if (isset($refund)) {
                    $refund->markAsFailed($e->getMessage());
                }

                throw $e;
            }
        });
    }

    /**
     * Authorize a payment (without capturing).
     *
     * @param PaymentRequest $request
     * @param array $context
     * @return PaymentResponse
     */
    public function authorizePayment(PaymentRequest $request, array $context = []): PaymentResponse
    {
        $request->setCapture(false);
        return $this->processPayment($request, $context);
    }

    /**
     * Capture a previously authorized payment.
     *
     * @param string $transactionId
     * @param float|null $amount
     * @param array $metadata
     * @return PaymentResponse
     * @throws PaymentGatewayException
     */
    public function capturePayment(string $transactionId, ?float $amount = null, array $metadata = []): PaymentResponse
    {
        return DB::transaction(function () use ($transactionId, $amount, $metadata) {
            try {
                $transaction = PaymentTransaction::where('transaction_id', $transactionId)
                                                ->orWhere('gateway_transaction_id', $transactionId)
                                                ->first();

                if (!$transaction) {
                    throw new PaymentGatewayException("Transaction not found");
                }

                if ($transaction->status !== 'authorized') {
                    throw new PaymentGatewayException("Transaction is not in authorized state");
                }

                $captureAmount = $amount ?? $transaction->amount;

                if ($captureAmount > $transaction->amount) {
                    throw new PaymentGatewayException("Capture amount cannot exceed authorized amount");
                }

                $gateway = $this->gatewayFactory->create($transaction->paymentMethod, $this->getEnvironment());
                $response = $gateway->capturePayment($transaction->gateway_transaction_id, $captureAmount, $metadata);

                $this->updateTransactionFromResponse($transaction, $response);

                return $response;

            } catch (\Exception $e) {
                Log::error('Payment capture failed', [
                    'error' => $e->getMessage(),
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                ]);

                throw $e;
            }
        });
    }

    /**
     * Void a previously authorized payment.
     *
     * @param string $transactionId
     * @param array $metadata
     * @return PaymentResponse
     * @throws PaymentGatewayException
     */
    public function voidPayment(string $transactionId, array $metadata = []): PaymentResponse
    {
        return DB::transaction(function () use ($transactionId, $metadata) {
            try {
                $transaction = PaymentTransaction::where('transaction_id', $transactionId)
                                                ->orWhere('gateway_transaction_id', $transactionId)
                                                ->first();

                if (!$transaction) {
                    throw new PaymentGatewayException("Transaction not found");
                }

                if ($transaction->status !== 'authorized') {
                    throw new PaymentGatewayException("Transaction is not in authorized state");
                }

                $gateway = $this->gatewayFactory->create($transaction->paymentMethod, $this->getEnvironment());
                $response = $gateway->voidPayment($transaction->gateway_transaction_id, $metadata);

                $this->updateTransactionFromResponse($transaction, $response);

                return $response;

            } catch (\Exception $e) {
                Log::error('Payment void failed', [
                    'error' => $e->getMessage(),
                    'transaction_id' => $transactionId,
                ]);

                throw $e;
            }
        });
    }

    /**
     * Get transaction details.
     *
     * @param string $transactionId
     * @return PaymentTransaction|null
     */
    public function getTransaction(string $transactionId): ?PaymentTransaction
    {
        return PaymentTransaction::where('transaction_id', $transactionId)
                                ->orWhere('gateway_transaction_id', $transactionId)
                                ->first();
    }

    /**
     * Get payment methods available for a context.
     *
     * @param array $context
     * @return array
     */
    public function getAvailablePaymentMethods(array $context): array
    {
        return $this->routingService->getAvailablePaymentMethods($context);
    }

    /**
     * Test payment method configuration.
     *
     * @param PaymentMethod $paymentMethod
     * @param string $environment
     * @return bool
     */
    public function testPaymentMethod(PaymentMethod $paymentMethod, string $environment = 'sandbox'): bool
    {
        try {
            return $this->gatewayFactory->testConnection($paymentMethod, $environment);
        } catch (\Exception $e) {
            Log::error('Payment method test failed', [
                'payment_method_id' => $paymentMethod->id,
                'environment' => $environment,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Create transaction record.
     *
     * @param PaymentRequest $request
     * @param PaymentMethod $paymentMethod
     * @param array $feeBreakdown
     * @param array $conversionResult
     * @param array $context
     * @return PaymentTransaction
     */
    protected function createTransactionRecord(
        PaymentRequest $request,
        PaymentMethod $paymentMethod,
        array $feeBreakdown,
        array $conversionResult,
        array $context
    ): PaymentTransaction {
        $currency = Currency::where('code', $request->getCurrency())->first();
        
        return PaymentTransaction::create([
            'transaction_id' => $this->generateTransactionId(),
            'payment_method_id' => $paymentMethod->id,
            'currency_id' => $currency->id,
            'amount' => $request->getAmount(),
            'base_currency_amount' => $conversionResult['converted_amount'],
            'exchange_rate_used' => $conversionResult['exchange_rate'],
            'customer_id' => $request->getCustomerId(),
            'transaction_type' => $request->getCapture() ? 'sale' : 'auth',
            'status' => 'pending',
            'fees_breakdown' => $feeBreakdown,
            'total_fees' => $feeBreakdown['total'] ?? 0,
            'net_amount' => $request->getAmount() - ($feeBreakdown['total'] ?? 0),
            'description' => $request->getDescription(),
            'order_id' => $request->getOrderId(),
            'invoice_number' => $request->getInvoiceNumber(),
            'customer_ip' => $request->getCustomerIp() ?? request()->ip(),
            'user_agent' => $request->getUserAgent() ?? request()->userAgent(),
            'billing_address' => $request->getBillingAddress(),
            'shipping_address' => $request->getShippingAddress(),
            'metadata' => array_merge($request->getMetadata() ?? [], $context),
            'is_test' => $this->getEnvironment() === 'sandbox',
        ]);
    }

    /**
     * Update transaction from gateway response.
     *
     * @param PaymentTransaction $transaction
     * @param PaymentResponse $response
     * @return void
     */
    protected function updateTransactionFromResponse(PaymentTransaction $transaction, PaymentResponse $response): void
    {
        $transaction->update([
            'gateway_transaction_id' => $response->getGatewayTransactionId(),
            'gateway_reference' => $response->getGatewayReference(),
            'status' => $response->getStatus(),
            'gateway_response' => $response->getGatewayResponse(),
            'error_details' => $response->isSuccess() ? null : [
                'code' => $response->getErrorCode(),
                'message' => $response->getErrorMessage(),
            ],
            'processed_at' => now(),
        ]);

        if ($response->getFees()) {
            $transaction->update([
                'fees_breakdown' => array_merge($transaction->fees_breakdown ?? [], [
                    'gateway_fees' => $response->getFees(),
                ]),
            ]);
        }
    }

    /**
     * Create refund record.
     *
     * @param RefundRequest $request
     * @param PaymentTransaction $originalTransaction
     * @param array $context
     * @return Refund
     */
    protected function createRefundRecord(RefundRequest $request, PaymentTransaction $originalTransaction, array $context): Refund
    {
        return Refund::create([
            'refund_id' => $this->generateRefundId(),
            'original_transaction_id' => $originalTransaction->id,
            'amount' => $request->getAmount(),
            'refund_type' => $request->getAmount() >= $originalTransaction->amount ? 'full' : 'partial',
            'reason' => $request->getReason() ?? 'requested_by_customer',
            'reason_details' => $request->getReasonDetails(),
            'status' => 'pending',
            'metadata' => array_merge($request->getMetadata() ?? [], $context),
        ]);
    }

    /**
     * Update refund from gateway response.
     *
     * @param Refund $refund
     * @param RefundResponse $response
     * @return void
     */
    protected function updateRefundFromResponse(Refund $refund, RefundResponse $response): void
    {
        $refund->update([
            'gateway_refund_id' => $response->getGatewayRefundId(),
            'status' => $response->getStatus(),
            'gateway_response' => $response->getGatewayResponse(),
            'fee_refunded' => $response->getFeeRefunded() ?? 0,
        ]);

        if ($response->isSuccess()) {
            $refund->markAsCompleted($response->getGatewayResponse());
        } elseif ($response->getErrorMessage()) {
            $refund->markAsFailed($response->getErrorMessage(), $response->getGatewayResponse());
        }
    }

    /**
     * Mark transaction as failed.
     *
     * @param PaymentTransaction $transaction
     * @param string $errorMessage
     * @return void
     */
    protected function markTransactionFailed(PaymentTransaction $transaction, string $errorMessage): void
    {
        $transaction->update([
            'status' => 'failed',
            'error_details' => ['message' => $errorMessage],
            'processed_at' => now(),
        ]);
    }

    /**
     * Generate unique transaction ID.
     *
     * @return string
     */
    protected function generateTransactionId(): string
    {
        return 'txn_' . Str::random(16) . '_' . time();
    }

    /**
     * Generate unique refund ID.
     *
     * @return string
     */
    protected function generateRefundId(): string
    {
        return 'ref_' . Str::random(16) . '_' . time();
    }

    /**
     * Get base currency.
     *
     * @return string
     */
    protected function getBaseCurrency(): string
    {
        return Currency::where('base_currency', true)->value('code') ?? 'USD';
    }

    /**
     * Get current environment.
     *
     * @return string
     */
    protected function getEnvironment(): string
    {
        return config('payment.environment', 'production');
    }
}
