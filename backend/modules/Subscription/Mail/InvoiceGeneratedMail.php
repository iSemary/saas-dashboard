<?php

namespace Modules\Subscription\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Subscription\Entities\SubscriptionInvoice;

class InvoiceGeneratedMail extends Mailable
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
            subject: "Invoice #{$this->invoice->invoice_number} - {$this->invoice->total_amount} {$this->invoice->currency?->code}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'subscription::emails.invoice-generated',
            with: [
                'invoice' => $this->invoice,
                'items' => $this->invoice->items,
                'paymentUrl' => $this->getPaymentUrl(),
            ],
        );
    }

    private function getPaymentUrl(): ?string
    {
        if ($this->invoice->status === 'paid') {
            return null;
        }

        // Generate tenant payment URL
        return route('tenant.billing.invoices.pay', ['id' => $this->invoice->id]);
    }
}
