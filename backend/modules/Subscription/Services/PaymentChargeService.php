<?php

namespace Modules\Subscription\Services;

use Modules\Payment\DTOs\PaymentRequest;
use Modules\Payment\Services\PaymentGatewayFactory;
use Modules\Payment\Services\PaymentGatewayService;
use Modules\Subscription\Entities\BrandBillingProfile;
use Modules\Subscription\Entities\SubscriptionInvoice;
use Modules\Subscription\Entities\SubscriptionPayment;

class PaymentChargeService
{
    public function __construct(
        private PaymentGatewayService $gatewayService,
        private PaymentGatewayFactory $gatewayFactory,
        private InvoiceGenerationService $invoiceService,
    ) {}

    /**
     * Charge an invoice using the brand's default payment method.
     */
    public function chargeInvoice(SubscriptionInvoice $invoice, ?string $gateway = null): array
    {
        $brandId = $invoice->brand_id;
        $profile = BrandBillingProfile::where('brand_id', $brandId)->first();

        if (!$profile) {
            throw new \InvalidArgumentException('Billing profile not found for brand');
        }

        $gateway = $gateway ?? $profile->default_gateway ?? 'stripe';
        
        // Check for sufficient account balance
        if ($profile->account_balance >= $invoice->total_amount) {
            // Use account credit
            $profile->deductBalance($invoice->total_amount);
            
            $payment = $this->recordPayment($invoice, [
                'amount' => $invoice->total_amount,
                'gateway' => 'account_credit',
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            $this->invoiceService->markAsPaid($invoice);

            return [
                'success' => true,
                'payment' => $payment,
                'method' => 'account_credit',
            ];
        }

        // Use payment gateway
        if (!$profile->hasPaymentMethod()) {
            throw new \InvalidArgumentException('No payment method on file');
        }

        $paymentMethod = $profile->defaultPaymentMethod;
        
        // Create payment request
        $paymentRequest = new PaymentRequest(
            amount: $invoice->total_amount,
            currency: $invoice->currency?->code ?? 'USD',
            description: "Invoice #{$invoice->invoice_number}",
            metadata: [
                'invoice_id' => $invoice->id,
                'brand_id' => $brandId,
                'subscription_id' => $invoice->subscription_id,
            ],
            customerId: $profile->getGatewayCustomerId($gateway),
            paymentMethodId: $paymentMethod->gateway_payment_method_id ?? null,
        );

        // Process payment
        $gatewayInstance = $this->gatewayFactory->make($gateway);
        $response = $gatewayInstance->charge($paymentRequest);

        // Record payment
        $payment = $this->recordPayment($invoice, [
            'amount' => $invoice->total_amount,
            'gateway' => $gateway,
            'gateway_payment_id' => $response->transactionId,
            'status' => $response->success ? 'completed' : 'failed',
            'failure_code' => $response->errorCode,
            'failure_message' => $response->errorMessage,
            'gateway_payload' => $response->rawResponse,
            'paid_at' => $response->success ? now() : null,
        ]);

        if ($response->success) {
            $this->invoiceService->markAsPaid($invoice, $response->transactionId);
        } else {
            $this->invoiceService->markAsFailed($invoice, $response->errorMessage ?? 'Payment failed');
        }

        return [
            'success' => $response->success,
            'payment' => $payment,
            'error' => $response->errorMessage,
        ];
    }

    /**
     * Retry a failed payment.
     */
    public function retryPayment(int $paymentId, ?string $newPaymentMethodId = null): array
    {
        $payment = SubscriptionPayment::findOrFail($paymentId);
        
        if ($payment->status !== 'failed') {
            throw new \InvalidArgumentException('Only failed payments can be retried');
        }

        $invoice = $payment->invoice;
        if (!$invoice) {
            throw new \InvalidArgumentException('Payment is not associated with an invoice');
        }

        // Update retry count
        $payment->increment('retry_count');

        // Retry with same or new payment method
        if ($newPaymentMethodId) {
            // Update profile with new default
            $profile = BrandBillingProfile::where('brand_id', $invoice->brand_id)->first();
            if ($profile) {
                $profile->update(['default_payment_method_id' => $newPaymentMethodId]);
            }
        }

        return $this->chargeInvoice($invoice, $payment->gateway);
    }

    /**
     * Create setup intent for adding a payment method.
     */
    public function createSetupIntent(int $brandId, string $gateway): array
    {
        $profile = BrandBillingProfile::firstOrCreate(
            ['brand_id' => $brandId],
            ['currency_code' => 'USD', 'default_gateway' => $gateway]
        );

        $gatewayInstance = $this->gatewayFactory->make($gateway);

        // If no customer exists, create one
        if (!$profile->getGatewayCustomerId($gateway)) {
            $customerId = $gatewayInstance->createCustomer([
                'brand_id' => $brandId,
                'email' => $profile->billing_email,
            ]);
            $profile->setGatewayCustomerId($gateway, $customerId);
        }

        // Create setup intent
        $setupIntent = $gatewayInstance->createSetupIntent([
            'customer_id' => $profile->getGatewayCustomerId($gateway),
        ]);

        return [
            'client_secret' => $setupIntent['client_secret'] ?? null,
            'customer_id' => $profile->getGatewayCustomerId($gateway),
        ];
    }

    /**
     * Attach a payment method to the brand profile.
     */
    public function attachPaymentMethod(int $brandId, string $gateway, string $paymentMethodId, bool $setAsDefault = true): \Modules\Payment\Entities\CustomerPaymentMethod
    {
        $profile = BrandBillingProfile::where('brand_id', $brandId)->first();
        
        if (!$profile) {
            throw new \InvalidArgumentException('Billing profile not found');
        }

        $gatewayInstance = $this->gatewayFactory->make($gateway);
        
        // Get payment method details from gateway
        $methodDetails = $gatewayInstance->getPaymentMethod($paymentMethodId);

        // Create local record
        $paymentMethod = \Modules\Payment\Entities\CustomerPaymentMethod::create([
            'brand_id' => $brandId,
            'gateway' => $gateway,
            'gateway_payment_method_id' => $paymentMethodId,
            'type' => $methodDetails['type'] ?? 'card',
            'last_four' => $methodDetails['last_four'] ?? null,
            'brand' => $methodDetails['brand'] ?? null,
            'exp_month' => $methodDetails['exp_month'] ?? null,
            'exp_year' => $methodDetails['exp_year'] ?? null,
            'is_default' => $setAsDefault,
        ]);

        if ($setAsDefault) {
            // Unset previous default
            \Modules\Payment\Entities\CustomerPaymentMethod::where('brand_id', $brandId)
                ->where('id', '!=', $paymentMethod->id)
                ->update(['is_default' => false]);
            
            $profile->update(['default_payment_method_id' => $paymentMethod->id]);
        }

        return $paymentMethod;
    }

    /**
     * Record a payment in the database.
     */
    private function recordPayment(SubscriptionInvoice $invoice, array $data): SubscriptionPayment
    {
        return SubscriptionPayment::create([
            'subscription_id' => $invoice->subscription_id,
            'invoice_id' => $invoice->id,
            'brand_id' => $invoice->brand_id,
            'payment_id' => 'pay_' . uniqid(),
            'amount' => $data['amount'],
            'currency_id' => $invoice->currency_id,
            'payment_type' => 'subscription',
            'payment_method_type' => $data['gateway'] === 'account_credit' ? 'credit' : 'card',
            'gateway' => $data['gateway'],
            'gateway_payment_id' => $data['gateway_payment_id'] ?? null,
            'status' => $data['status'],
            'failure_code' => $data['failure_code'] ?? null,
            'failure_message' => $data['failure_message'] ?? null,
            'gateway_payload' => $data['gateway_payload'] ?? null,
            'paid_at' => $data['paid_at'] ?? null,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Get or create checkout session for initial subscription.
     */
    public function createCheckoutSession(SubscriptionInvoice $invoice, string $gateway, array $options = []): array
    {
        $profile = BrandBillingProfile::firstOrCreate(
            ['brand_id' => $invoice->brand_id],
            ['currency_code' => $invoice->currency?->code ?? 'USD', 'default_gateway' => $gateway]
        );

        $gatewayInstance = $this->gatewayFactory->make($gateway);

        // Create customer if needed
        $customerId = $profile->getGatewayCustomerId($gateway);
        if (!$customerId) {
            $customerId = $gatewayInstance->createCustomer([
                'brand_id' => $invoice->brand_id,
                'email' => $options['email'] ?? $profile->billing_email,
            ]);
            $profile->setGatewayCustomerId($gateway, $customerId);
        }

        // Create checkout session
        $session = $gatewayInstance->createCheckoutSession([
            'customer_id' => $customerId,
            'amount' => $invoice->total_amount,
            'currency' => $invoice->currency?->code ?? 'USD',
            'description' => "Invoice #{$invoice->invoice_number}",
            'success_url' => $options['success_url'] ?? null,
            'cancel_url' => $options['cancel_url'] ?? null,
            'metadata' => [
                'invoice_id' => $invoice->id,
                'brand_id' => $invoice->brand_id,
            ],
        ]);

        return [
            'session_id' => $session['id'] ?? null,
            'checkout_url' => $session['url'] ?? null,
            'customer_id' => $customerId,
        ];
    }
}
