<?php

namespace Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Payment\Services\PaymentGatewayService;
use Modules\Payment\DTOs\PaymentRequest;
use Modules\Payment\DTOs\RefundRequest;
use Modules\Payment\Http\Requests\Api\ProcessPaymentRequest;
use Modules\Payment\Http\Requests\Api\ProcessRefundRequest;
use Modules\Payment\Http\Requests\Api\CapturePaymentRequest;
use Modules\Payment\Http\Requests\Api\VoidPaymentRequest;
use Modules\Payment\Exceptions\PaymentGatewayException;
use Illuminate\Support\Facades\Log;

class PaymentApiController extends Controller
{
    protected PaymentGatewayService $paymentService;

    public function __construct(PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
        
        // Apply API middleware
        $this->middleware(['auth:sanctum', 'throttle:payment']);
    }

    /**
     * Process a payment transaction.
     *
     * @param ProcessPaymentRequest $request
     * @return JsonResponse
     */
    public function processPayment(ProcessPaymentRequest $request): JsonResponse
    {
        try {
            $paymentRequest = new PaymentRequest(
                $request->validated('amount'),
                $request->validated('currency')
            );

            // Set optional fields
            if ($request->has('customer_id')) {
                $paymentRequest->setCustomerId($request->validated('customer_id'));
            }

            if ($request->has('payment_method_id')) {
                $paymentRequest->setPaymentMethodId($request->validated('payment_method_id'));
            }

            if ($request->has('payment_method_data')) {
                $paymentRequest->setPaymentMethodData($request->validated('payment_method_data'));
            }

            if ($request->has('description')) {
                $paymentRequest->setDescription($request->validated('description'));
            }

            if ($request->has('metadata')) {
                $paymentRequest->setMetadata($request->validated('metadata'));
            }

            if ($request->has('billing_address')) {
                $paymentRequest->setBillingAddress($request->validated('billing_address'));
            }

            if ($request->has('shipping_address')) {
                $paymentRequest->setShippingAddress($request->validated('shipping_address'));
            }

            if ($request->has('capture')) {
                $paymentRequest->setCapture($request->validated('capture'));
            }

            if ($request->has('statement_descriptor')) {
                $paymentRequest->setStatementDescriptor($request->validated('statement_descriptor'));
            }

            if ($request->has('receipt_email')) {
                $paymentRequest->setReceiptEmail($request->validated('receipt_email'));
            }

            if ($request->has('order_id')) {
                $paymentRequest->setOrderId($request->validated('order_id'));
            }

            if ($request->has('invoice_number')) {
                $paymentRequest->setInvoiceNumber($request->validated('invoice_number'));
            }

            if ($request->has('return_url')) {
                $paymentRequest->setReturnUrl($request->validated('return_url'));
            }

            if ($request->has('cancel_url')) {
                $paymentRequest->setCancelUrl($request->validated('cancel_url'));
            }

            // Set request context
            $paymentRequest->setCustomerIp($request->ip());
            $paymentRequest->setUserAgent($request->userAgent());

            // Build context for routing and validation
            $context = [
                'country' => $request->validated('country', 'US'),
                'customer_segment' => $request->validated('customer_segment', 'all'),
                'is_recurring' => $request->validated('is_recurring', false),
                'risk_score' => $request->validated('risk_score', 0),
                'currency' => $request->validated('currency'),
                'amount' => $request->validated('amount'),
                'customer_id' => $request->validated('customer_id'),
            ];

            $response = $this->paymentService->processPayment($paymentRequest, $context);

            return response()->json([
                'success' => $response->isSuccess(),
                'data' => $response->toArray(),
                'message' => $response->isSuccess() ? 'Payment processed successfully' : 'Payment processing failed'
            ], $response->isSuccess() ? 200 : 400);

        } catch (PaymentGatewayException $e) {
            Log::error('Payment API error', [
                'error' => $e->getMessage(),
                'gateway_code' => $e->getGatewayCode(),
                'transaction_id' => $e->getTransactionId(),
                'request_data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'gateway_code' => $e->getGatewayCode(),
                    'transaction_id' => $e->getTransactionId(),
                ],
            ], 400);

        } catch (\Exception $e) {
            Log::error('Payment API unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INTERNAL_ERROR',
                    'message' => translate('message.operation_failed'),
                ],
            ], 500);
        }
    }

    /**
     * Authorize a payment (without capturing).
     *
     * @param ProcessPaymentRequest $request
     * @return JsonResponse
     */
    public function authorizePayment(ProcessPaymentRequest $request): JsonResponse
    {
        // Force capture to false for authorization
        $request->merge(['capture' => false]);
        
        return $this->processPayment($request);
    }

    /**
     * Capture a previously authorized payment.
     *
     * @param CapturePaymentRequest $request
     * @param string $transactionId
     * @return JsonResponse
     */
    public function capturePayment(CapturePaymentRequest $request, string $transactionId): JsonResponse
    {
        try {
            $amount = $request->validated('amount');
            $metadata = $request->validated('metadata', []);

            $response = $this->paymentService->capturePayment($transactionId, $amount, $metadata);

            return response()->json([
                'success' => $response->isSuccess(),
                'data' => $response->toArray(),
                'message' => $response->isSuccess() ? 'Payment captured successfully' : 'Payment capture failed'
            ], $response->isSuccess() ? 200 : 400);

        } catch (PaymentGatewayException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'gateway_code' => $e->getGatewayCode(),
                ],
            ], 400);

        } catch (\Exception $e) {
            Log::error('Capture API error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INTERNAL_ERROR',
                    'message' => translate('message.operation_failed'),
                ],
            ], 500);
        }
    }

    /**
     * Void a previously authorized payment.
     *
     * @param VoidPaymentRequest $request
     * @param string $transactionId
     * @return JsonResponse
     */
    public function voidPayment(VoidPaymentRequest $request, string $transactionId): JsonResponse
    {
        try {
            $metadata = $request->validated('metadata', []);

            $response = $this->paymentService->voidPayment($transactionId, $metadata);

            return response()->json([
                'success' => $response->isSuccess(),
                'data' => $response->toArray(),
                'message' => $response->isSuccess() ? 'Payment voided successfully' : 'Payment void failed'
            ], $response->isSuccess() ? 200 : 400);

        } catch (PaymentGatewayException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'gateway_code' => $e->getGatewayCode(),
                ],
            ], 400);

        } catch (\Exception $e) {
            Log::error('Void API error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INTERNAL_ERROR',
                    'message' => translate('message.operation_failed'),
                ],
            ], 500);
        }
    }

    /**
     * Process a refund.
     *
     * @param ProcessRefundRequest $request
     * @return JsonResponse
     */
    public function processRefund(ProcessRefundRequest $request): JsonResponse
    {
        try {
            $refundRequest = new RefundRequest(
                $request->validated('transaction_id'),
                $request->validated('amount')
            );

            if ($request->has('reason')) {
                $refundRequest->setReason($request->validated('reason'));
            }

            if ($request->has('reason_details')) {
                $refundRequest->setReasonDetails($request->validated('reason_details'));
            }

            if ($request->has('metadata')) {
                $refundRequest->setMetadata($request->validated('metadata'));
            }

            if ($request->has('refund_fees')) {
                $refundRequest->setRefundFees($request->validated('refund_fees'));
            }

            $context = [
                'initiated_by' => auth()->id(),
                'api_request' => true,
            ];

            $response = $this->paymentService->processRefund($refundRequest, $context);

            return response()->json([
                'success' => $response->isSuccess(),
                'data' => $response->toArray(),
                'message' => $response->isSuccess() ? 'Refund processed successfully' : 'Refund processing failed'
            ], $response->isSuccess() ? 200 : 400);

        } catch (PaymentGatewayException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'gateway_code' => $e->getGatewayCode(),
                ],
            ], 400);

        } catch (\Exception $e) {
            Log::error('Refund API error', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INTERNAL_ERROR',
                    'message' => translate('message.operation_failed'),
                ],
            ], 500);
        }
    }

    /**
     * Get transaction details.
     *
     * @param string $transactionId
     * @return JsonResponse
     */
    public function getTransaction(string $transactionId): JsonResponse
    {
        try {
            $transaction = $this->paymentService->getTransaction($transactionId);

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'TRANSACTION_NOT_FOUND',
                        'message' => translate('message.resource_not_found'),
                    ],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $transaction->transaction_id,
                    'gateway_transaction_id' => $transaction->gateway_transaction_id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency->code,
                    'status' => $transaction->status,
                    'transaction_type' => $transaction->transaction_type,
                    'payment_method' => $transaction->paymentMethod->name,
                    'customer_id' => $transaction->customer_id,
                    'created_at' => $transaction->created_at,
                    'processed_at' => $transaction->processed_at,
                    'metadata' => $transaction->metadata,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Get transaction API error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INTERNAL_ERROR',
                    'message' => translate('message.operation_failed'),
                ],
            ], 500);
        }
    }

    /**
     * Get available payment methods.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPaymentMethods(Request $request): JsonResponse
    {
        try {
            $context = [
                'country' => $request->query('country', 'US'),
                'currency' => $request->query('currency', 'USD'),
                'amount' => $request->query('amount', 0),
                'customer_segment' => $request->query('customer_segment', 'all'),
            ];

            $paymentMethods = $this->paymentService->getAvailablePaymentMethods($context);

            $formattedMethods = $paymentMethods->map(function ($method) {
                return [
                    'id' => $method->id,
                    'name' => $method->name,
                    'processor_type' => $method->processor_type,
                    'supported_currencies' => $method->supported_currencies,
                    'is_global' => $method->is_global,
                    'features' => $method->features,
                    'success_rate' => $method->success_rate,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedMethods,
            ]);

        } catch (\Exception $e) {
            Log::error('Get payment methods API error', [
                'error' => $e->getMessage(),
                'context' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INTERNAL_ERROR',
                    'message' => translate('message.operation_failed'),
                ],
            ], 500);
        }
    }

    /**
     * Test payment method configuration.
     *
     * @param Request $request
     * @param int $paymentMethodId
     * @return JsonResponse
     */
    public function testPaymentMethod(Request $request, int $paymentMethodId): JsonResponse
    {
        try {
            $paymentMethod = \Modules\Payment\Entities\PaymentMethod::find($paymentMethodId);

            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'PAYMENT_METHOD_NOT_FOUND',
                        'message' => translate('message.resource_not_found'),
                    ],
                ], 404);
            }

            $environment = $request->query('environment', 'sandbox');
            $result = $this->paymentService->testPaymentMethod($paymentMethod, $environment);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Payment method test successful' : 'Payment method test failed',
                'data' => [
                    'payment_method_id' => $paymentMethodId,
                    'environment' => $environment,
                    'test_result' => $result,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Test payment method API error', [
                'error' => $e->getMessage(),
                'payment_method_id' => $paymentMethodId,
            ]);

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INTERNAL_ERROR',
                    'message' => translate('message.operation_failed'),
                ],
            ], 500);
        }
    }
}
