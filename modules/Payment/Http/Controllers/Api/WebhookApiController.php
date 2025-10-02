<?php

namespace Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Payment\Services\PaymentGatewayFactory;
use Modules\Payment\DTOs\WebhookRequest;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Entities\PaymentGatewayLog;
use Illuminate\Support\Facades\Log;

class WebhookApiController extends Controller
{
    protected PaymentGatewayFactory $gatewayFactory;

    public function __construct(PaymentGatewayFactory $gatewayFactory)
    {
        $this->gatewayFactory = $gatewayFactory;
        
        // No authentication for webhooks - they use signature verification
        $this->middleware(['throttle:webhook']);
    }

    /**
     * Handle Stripe webhooks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function stripe(Request $request): JsonResponse
    {
        return $this->handleWebhook($request, 'stripe');
    }

    /**
     * Handle PayPal webhooks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function paypal(Request $request): JsonResponse
    {
        return $this->handleWebhook($request, 'paypal');
    }

    /**
     * Handle Razorpay webhooks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function razorpay(Request $request): JsonResponse
    {
        return $this->handleWebhook($request, 'razorpay');
    }

    /**
     * Handle Adyen webhooks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function adyen(Request $request): JsonResponse
    {
        return $this->handleWebhook($request, 'adyen');
    }

    /**
     * Handle Square webhooks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function square(Request $request): JsonResponse
    {
        return $this->handleWebhook($request, 'square');
    }

    /**
     * Handle generic gateway webhooks.
     *
     * @param Request $request
     * @param string $gateway
     * @return JsonResponse
     */
    public function generic(Request $request, string $gateway): JsonResponse
    {
        return $this->handleWebhook($request, $gateway);
    }

    /**
     * Handle webhook for any gateway.
     *
     * @param Request $request
     * @param string $gatewayType
     * @return JsonResponse
     */
    protected function handleWebhook(Request $request, string $gatewayType): JsonResponse
    {
        $startTime = microtime(true);
        $correlationId = uniqid('webhook_', true);

        try {
            // Get payment method for this gateway type
            $paymentMethod = PaymentMethod::where('processor_type', $gatewayType)
                                         ->where('status', 'active')
                                         ->first();

            if (!$paymentMethod) {
                Log::warning("No active payment method found for gateway: {$gatewayType}");
                return response()->json(['error' => 'Gateway not configured'], 404);
            }

            // Create webhook request DTO
            $webhookRequest = new WebhookRequest(
                $request->getContent(),
                $request->headers->all()
            );

            $webhookRequest->setGatewayName($gatewayType);

            // Get signature from headers (different for each gateway)
            $signature = $this->extractSignature($request, $gatewayType);
            if ($signature) {
                $webhookRequest->setSignature($signature);
            }

            // Log incoming webhook
            $this->logWebhookRequest($paymentMethod->id, $webhookRequest, $correlationId);

            // Create gateway instance and handle webhook
            $gateway = $this->gatewayFactory->create($paymentMethod, config('payment.environment', 'production'));

            // Verify webhook signature if signature is provided
            if ($signature) {
                $webhookSecret = $paymentMethod->getConfigValue('webhook_secret', config('payment.environment', 'production'));
                
                if ($webhookSecret && !$gateway->verifyWebhookSignature($request->getContent(), $signature, $webhookSecret)) {
                    Log::warning("Invalid webhook signature for {$gatewayType}", [
                        'correlation_id' => $correlationId,
                        'signature' => $signature,
                    ]);
                    
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            // Process webhook
            $response = $gateway->handleWebhook($webhookRequest);
            $processingTime = (microtime(true) - $startTime) * 1000;

            // Log webhook response
            $this->logWebhookResponse($paymentMethod->id, $response, $processingTime, $correlationId);

            // Process webhook actions
            $this->processWebhookActions($response);

            return response()->json($response->toHttpResponse(), $response->getHttpStatus());

        } catch (\Exception $e) {
            $processingTime = (microtime(true) - $startTime) * 1000;
            
            Log::error("Webhook processing error for {$gatewayType}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'correlation_id' => $correlationId,
                'processing_time_ms' => $processingTime,
            ]);

            // Log error
            if (isset($paymentMethod)) {
                PaymentGatewayLog::createLog($paymentMethod->id, 'error', 'webhook', [
                    'error_message' => $e->getMessage(),
                    'processing_time_ms' => round($processingTime),
                    'correlation_id' => $correlationId,
                    'is_webhook' => true,
                ]);
            }

            return response()->json([
                'error' => 'Webhook processing failed',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Extract signature from request headers based on gateway type.
     *
     * @param Request $request
     * @param string $gatewayType
     * @return string|null
     */
    protected function extractSignature(Request $request, string $gatewayType): ?string
    {
        switch ($gatewayType) {
            case 'stripe':
                return $request->header('Stripe-Signature');
            
            case 'paypal':
                return $request->header('PAYPAL-TRANSMISSION-SIG');
            
            case 'razorpay':
                return $request->header('X-Razorpay-Signature');
            
            case 'adyen':
                return $request->header('Authorization');
            
            case 'square':
                return $request->header('X-Square-Signature');
            
            default:
                // Try common signature headers
                return $request->header('X-Signature') 
                    ?? $request->header('X-Hub-Signature-256')
                    ?? $request->header('X-Webhook-Signature');
        }
    }

    /**
     * Log incoming webhook request.
     *
     * @param int $paymentMethodId
     * @param WebhookRequest $webhookRequest
     * @param string $correlationId
     * @return void
     */
    protected function logWebhookRequest(int $paymentMethodId, WebhookRequest $webhookRequest, string $correlationId): void
    {
        PaymentGatewayLog::createLog($paymentMethodId, 'info', 'webhook_received', [
            'request_data' => $webhookRequest->getParsedPayload(),
            'headers' => $webhookRequest->getHeaders(),
            'correlation_id' => $correlationId,
            'is_webhook' => true,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Log webhook response.
     *
     * @param int $paymentMethodId
     * @param object $response
     * @param float $processingTime
     * @param string $correlationId
     * @return void
     */
    protected function logWebhookResponse(int $paymentMethodId, $response, float $processingTime, string $correlationId): void
    {
        PaymentGatewayLog::createLog($paymentMethodId, 'info', 'webhook_processed', [
            'response_data' => $response->toArray(),
            'processing_time_ms' => round($processingTime),
            'correlation_id' => $correlationId,
            'is_webhook' => true,
        ]);
    }

    /**
     * Process webhook actions.
     *
     * @param object $response
     * @return void
     */
    protected function processWebhookActions($response): void
    {
        $actions = $response->getActions();
        
        if (!$actions) {
            return;
        }

        foreach ($actions as $action) {
            try {
                $this->processWebhookAction($action);
            } catch (\Exception $e) {
                Log::error('Webhook action processing failed', [
                    'action' => $action,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Process a single webhook action.
     *
     * @param array $action
     * @return void
     */
    protected function processWebhookAction(array $action): void
    {
        $actionType = $action['action'] ?? null;
        $actionData = $action['data'] ?? [];

        switch ($actionType) {
            case 'update_transaction_status':
                $this->updateTransactionStatus($actionData);
                break;

            case 'create_chargeback':
                $this->createChargeback($actionData);
                break;

            case 'update_subscription_status':
                $this->updateSubscriptionStatus($actionData);
                break;

            case 'send_notification':
                $this->sendNotification($actionData);
                break;

            default:
                Log::info('Unknown webhook action', ['action' => $actionType, 'data' => $actionData]);
        }
    }

    /**
     * Update transaction status.
     *
     * @param array $data
     * @return void
     */
    protected function updateTransactionStatus(array $data): void
    {
        $transactionId = $data['transaction_id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$transactionId || !$status) {
            return;
        }

        $transaction = \Modules\Payment\Entities\PaymentTransaction::where('gateway_transaction_id', $transactionId)
                                                                  ->orWhere('transaction_id', $transactionId)
                                                                  ->first();

        if ($transaction) {
            $transaction->update([
                'status' => $status,
                'processed_at' => now(),
            ]);

            Log::info('Transaction status updated via webhook', [
                'transaction_id' => $transactionId,
                'old_status' => $transaction->getOriginal('status'),
                'new_status' => $status,
            ]);
        }
    }

    /**
     * Create chargeback record.
     *
     * @param array $data
     * @return void
     */
    protected function createChargeback(array $data): void
    {
        $transactionId = $data['transaction_id'] ?? null;
        $amount = $data['amount'] ?? null;
        $reasonCode = $data['reason_code'] ?? null;

        if (!$transactionId || !$amount) {
            return;
        }

        $transaction = \Modules\Payment\Entities\PaymentTransaction::where('gateway_transaction_id', $transactionId)
                                                                  ->orWhere('transaction_id', $transactionId)
                                                                  ->first();

        if ($transaction) {
            \Modules\Payment\Entities\Chargeback::create([
                'chargeback_id' => 'cb_' . uniqid() . '_' . time(),
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'reason_code' => $reasonCode,
                'status' => 'received',
                'received_at' => now(),
            ]);

            // Update transaction status
            $transaction->update(['status' => 'charged_back']);

            Log::info('Chargeback created via webhook', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'reason_code' => $reasonCode,
            ]);
        }
    }

    /**
     * Update subscription status.
     *
     * @param array $data
     * @return void
     */
    protected function updateSubscriptionStatus(array $data): void
    {
        // This would integrate with a subscription module if available
        Log::info('Subscription status update requested via webhook', $data);
    }

    /**
     * Send notification.
     *
     * @param array $data
     * @return void
     */
    protected function sendNotification(array $data): void
    {
        // This would integrate with a notification system
        Log::info('Notification requested via webhook', $data);
    }
}
