<?php

namespace Modules\Subscription\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Subscription\Entities\SubscriptionInvoice;

class InvoicePaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public SubscriptionInvoice $invoice;

    public function __construct(SubscriptionInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment Confirmation - Invoice #{$this->invoice->invoice_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'subscription::emails.invoice-paid',
            with: [
                'invoice' => $this->invoice,
                'receiptUrl' => $this->getReceiptUrl(),
            ],
        );
    }

    private function getReceiptUrl(): string
    {
        return route('tenant.billing.invoices.show', ['id' => $this->invoice->id]);
    }
}
