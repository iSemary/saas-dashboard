<?php

namespace Modules\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Email\Services\DynamicEmailService;

class RegistrationMail extends Mailable {
    use Queueable, SerializesModels;
    private $data;
    private $dynamicEmailService;
    
    /**
     * Create a new message instance.
     */
    public function __construct($data) {
        $this->data = $data;
        $this->dynamicEmailService = app(DynamicEmailService::class);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope {
        $template = $this->dynamicEmailService->getTemplateByName('User Registration');
        $subject = $template ? $template->subject : 'Thank you for registration';
        
        // Process subject with variables
        $processedSubject = $this->processTemplate($subject, $this->data);
        
        return new Envelope(subject: $processedSubject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content {
        $template = $this->dynamicEmailService->getTemplateByName('User Registration');
        
        if ($template) {
            // Use dynamic template
            $processedBody = $this->processTemplate($template->body, $this->data);
            return new Content(htmlString: $processedBody);
        } else {
            // Fallback to old view
            return new Content(view: 'mails.registration', with: ['body' => $this->data]);
        }
    }

    /**
     * Process template variables
     */
    private function processTemplate(string $content, array $variables): string
    {
        // Add default variables
        $defaultVariables = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'verification_url' => env('APP_URL') . '/verify/email?token=' . $variables['token'],
        ];

        $variables = array_merge($defaultVariables, $variables);

        // Replace variables in template
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }
}
