<?php

namespace Modules\Subscription\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Services\InvoiceGenerationService;
use Modules\Subscription\Services\PaymentChargeService;

class GenerateBillingInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300;
    public $queue = 'billing';

    public function __construct()
    {
        //
    }

    public function handle(InvoiceGenerationService $invoiceService, PaymentChargeService $paymentService): void
    {
        Log::info('GenerateBillingInvoicesJob: Starting invoice generation');

        try {
            // Generate invoices for all subscriptions due for billing
            $results = $invoiceService->generateDueInvoices();

            Log::info('GenerateBillingInvoicesJob: Generated ' . $results['generated'] . ' invoices');

            if (!empty($results['errors'])) {
                foreach ($results['errors'] as $error) {
                    Log::error('GenerateBillingInvoicesJob: Failed for subscription ' . $error['subscription_id'] . ': ' . $error['error']);
                }
            }

            // Process auto-pay for invoices that are pending
            $pendingInvoices = \Modules\Subscription\Entities\SubscriptionInvoice::where('status', 'pending')
                ->where('due_date', '<=', now()->addDays(3))
                ->with('brand.billingProfile')
                ->get();

            foreach ($pendingInvoices as $invoice) {
                $profile = $invoice->brand?->billingProfile;
                
                if ($profile && $profile->auto_pay && $profile->hasPaymentMethod()) {
                    try {
                        $result = $paymentService->chargeInvoice($invoice);
                        
                        if ($result['success']) {
                            Log::info('GenerateBillingInvoicesJob: Auto-paid invoice ' . $invoice->id);
                        } else {
                            Log::warning('GenerateBillingInvoicesJob: Auto-pay failed for invoice ' . $invoice->id . ': ' . $result['error']);
                        }
                    } catch (\Exception $e) {
                        Log::error('GenerateBillingInvoicesJob: Auto-pay exception for invoice ' . $invoice->id . ': ' . $e->getMessage());
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('GenerateBillingInvoicesJob: Failed with exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
