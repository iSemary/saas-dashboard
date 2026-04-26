<?php

namespace Modules\Subscription\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Subscription\Entities\SubscriptionInvoice;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public SubscriptionInvoice $invoice;
    public ?string $failureReason;

    public function __construct(SubscriptionInvoice $invoice, ?string $failureReason = null)
    {
        $this->invoice = $invoice;
        $this->failureReason = $failureReason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Action Required: Payment Failed - Invoice #{$this->invoice->invoice_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'subscription::emails.payment-failed',
            with: [
                'invoice' => $this->invoice,
                'failureReason' => $this->failureReason,
                'paymentUrl' => $this->getPaymentUrl(),
            ],
        );
    }

    private function getPaymentUrl(): string
    {
        return route('tenant.billing.invoices.pay', ['id' => $this->invoice->id]);
    }
}
