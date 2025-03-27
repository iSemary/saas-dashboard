<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BaseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $header;
    public $footer;
    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->header = configuration("emails.header");
        $this->footer = configuration("emails.footer");
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->data['subject'] ?? env("APP_NAME"),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.base',
            with: [
                'header' => $this->header,
                'body' => $this->data['body'],
                'footer' => $this->footer,
            ]
        );
    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
