<?php

namespace Modules\Email\Services;

use Modules\Email\Entities\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\BaseMail;

class DynamicEmailService
{
    protected $emailTemplateService;

    public function __construct(EmailTemplateService $emailTemplateService)
    {
        $this->emailTemplateService = $emailTemplateService;
    }

    /**
     * Send email using dynamic template
     *
     * @param string $templateName
     * @param string $to
     * @param array $variables
     * @param array $options
     * @return bool
     */
    public function sendTemplateEmail(string $templateName, string $to, array $variables = [], array $options = []): bool
    {
        try {
            $template = $this->getTemplateByName($templateName);
            
            if (!$template) {
                throw new \Exception("Email template '{$templateName}' not found");
            }

            $processedSubject = $this->processTemplate($template->subject, $variables);
            $processedBody = $this->processTemplate($template->body, $variables);

            $emailData = [
                'subject' => $processedSubject,
                'body' => $processedBody,
                'email' => $to,
                'email_credential_id' => $options['email_credential_id'] ?? 1,
            ];

            // Add any additional data
            if (isset($options['additional_data'])) {
                $emailData = array_merge($emailData, $options['additional_data']);
            }

            Mail::to($to)->send(new BaseMail($emailData));
            
            return true;
        } catch (\Exception $e) {
            Log::error('Dynamic email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get template by name
     *
     * @param string $name
     * @return EmailTemplate|null
     */
    public function getTemplateByName(string $name): ?EmailTemplate
    {
        return EmailTemplate::where('name', $name)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Process template variables
     *
     * @param string $content
     * @param array $variables
     * @return string
     */
    protected function processTemplate(string $content, array $variables): string
    {
        // Add default variables
        $defaultVariables = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
        ];

        $variables = array_merge($defaultVariables, $variables);

        // Replace variables in template
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }

    /**
     * Get all available templates
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTemplates()
    {
        return EmailTemplate::where('status', 'active')->get();
    }

    /**
     * Check if template exists
     *
     * @param string $name
     * @return bool
     */
    public function templateExists(string $name): bool
    {
        return EmailTemplate::where('name', $name)
            ->where('status', 'active')
            ->exists();
    }
}
