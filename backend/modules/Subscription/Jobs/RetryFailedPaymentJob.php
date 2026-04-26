<?php

namespace Modules\Subscription\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Entities\SubscriptionPayment;
use Modules\Subscription\Services\PaymentChargeService;

class RetryFailedPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    public $queue = 'billing';

    private int $paymentId;

    public function __construct(int $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function handle(PaymentChargeService $paymentService): void
    {
        Log::info('RetryFailedPaymentJob: Retrying payment ' . $this->paymentId);

        try {
            $result = $paymentService->retryPayment($this->paymentId);

            if ($result['success']) {
                Log::info('RetryFailedPaymentJob: Payment ' . $this->paymentId . ' succeeded');
            } else {
                Log::warning('RetryFailedPaymentJob: Payment ' . $this->paymentId . ' failed again: ' . $result['error']);
            }
        } catch (\Exception $e) {
            Log::error('RetryFailedPaymentJob: Exception for payment ' . $this->paymentId . ': ' . $e->getMessage());
            throw $e;
        }
    }
}
