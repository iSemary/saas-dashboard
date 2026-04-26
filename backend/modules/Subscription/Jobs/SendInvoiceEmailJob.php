<?php

namespace Modules\Subscription\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Subscription\Entities\SubscriptionInvoice;
use Modules\Subscription\Mail\InvoiceGeneratedMail;
use Modules\Subscription\Mail\InvoicePaidMail;
use Modules\Subscription\Mail\PaymentFailedMail;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    public $queue = 'billing';

    private int $invoiceId;
    private string $type; // 'generated', 'paid', 'failed'

    public function __construct(int $invoiceId, string $type = 'generated')
    {
        $this->invoiceId = $invoiceId;
        $this->type = $type;
    }

    public function handle(): void
    {
        Log::info("SendInvoiceEmailJob: Sending {$this->type} email for invoice {$this->invoiceId}");

        $invoice = SubscriptionInvoice::with(['brand', 'user', 'items', 'currency'])->find($this->invoiceId);

        if (!$invoice) {
            Log::warning("SendInvoiceEmailJob: Invoice {$this->invoiceId} not found");
            return;
        }

        $profile = $invoice->brand?->billingProfile;
        $email = $profile?->billing_email ?? $invoice->user?->email;

        if (!$email) {
            Log::warning("SendInvoiceEmailJob: No email found for invoice {$this->invoiceId}");
            return;
        }

        try {
            match ($this->type) {
                'generated' => $this->sendGeneratedEmail($invoice, $email),
                'paid' => $this->sendPaidEmail($invoice, $email),
                'failed' => $this->sendFailedEmail($invoice, $email),
                default => Log::warning("SendInvoiceEmailJob: Unknown email type {$this->type}"),
            };
        } catch (\Exception $e) {
            Log::error("SendInvoiceEmailJob: Failed to send email for invoice {$this->invoiceId}: " . $e->getMessage());
            throw $e;
        }
    }

    private function sendGeneratedEmail(SubscriptionInvoice $invoice, string $email): void
    {
        Mail::to($email)
            ->cc($invoice->brand?->billingProfile?->invoice_email_cc ?? [])
            ->send(new InvoiceGeneratedMail($invoice));
    }

    private function sendPaidEmail(SubscriptionInvoice $invoice, string $email): void
    {
        Mail::to($email)
            ->cc($invoice->brand?->billingProfile?->invoice_email_cc ?? [])
            ->send(new InvoicePaidMail($invoice));
    }

    private function sendFailedEmail(SubscriptionInvoice $invoice, string $email): void
    {
        Mail::to($email)
            ->cc($invoice->brand?->billingProfile?->invoice_email_cc ?? [])
            ->send(new PaymentFailedMail($invoice));
    }
}
