<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Strategies\AutomationAction;

use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Domain\Exceptions\AutomationExecutionException;

/**
 * Send an email using the Email module.
 */
class SendEmailAction implements AutomationActionStrategyInterface
{
    public function supports(string $actionType): bool
    {
        return $actionType === 'send_email';
    }

    public function execute(array $actionConfig, object $model, array $context): void
    {
        $templateId = $actionConfig['template_id'] ?? null;
        $subject = $actionConfig['subject'] ?? null;
        $body = $actionConfig['body'] ?? null;
        $to = $actionConfig['to'] ?? null;

        if (!$templateId && (!$subject || !$body)) {
            throw new AutomationExecutionException(translate('message.validation_failed'));
        }

        // Resolve recipient email
        $recipientEmail = $this->resolveRecipient($to, $model);

        if (!$recipientEmail) {
            throw new AutomationExecutionException(translate('message.operation_failed'));
        }

        // Use Email module to send
        $emailIntegration = app(\Modules\CRM\Infrastructure\Integrations\EmailIntegration::class);

        $emailIntegration->send([
            'to' => $recipientEmail,
            'subject' => $subject,
            'body' => $body,
            'template_id' => $templateId,
            'related_type' => get_class($model),
            'related_id' => $model->id ?? null,
            'track_opens' => true,
            'track_clicks' => true,
        ]);
    }

    public function getName(): string
    {
        return 'Send Email';
    }

    public function getDescription(): string
    {
        return 'Send an email to the contact using a template or custom content.';
    }

    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'template_id',
                'type' => 'number',
                'required' => false,
                'label' => 'Email Template ID',
                'help' => 'Use a saved template. If provided, subject/body are optional.',
            ],
            [
                'name' => 'subject',
                'type' => 'string',
                'required' => false,
                'label' => 'Subject',
            ],
            [
                'name' => 'body',
                'type' => 'richtext',
                'required' => false,
                'label' => 'Email Body',
            ],
            [
                'name' => 'to',
                'type' => 'select',
                'required' => true,
                'label' => 'Recipient',
                'options' => [
                    'lead_email' => 'Lead/Contact Email',
                    'assigned_user' => 'Assigned User Email',
                    'custom' => 'Custom Email',
                ],
            ],
            [
                'name' => 'custom_email',
                'type' => 'email',
                'required' => false,
                'label' => 'Custom Email Address',
                'help' => 'Only used if Recipient is set to "Custom Email"',
            ],
        ];
    }

    private function resolveRecipient(string $to, object $model): ?string
    {
        return match ($to) {
            'lead_email' => $model->email ?? null,
            'contact_email' => $model->email ?? null,
            'assigned_user' => $model->assignedUser?->email ?? null,
            'custom' => null, // Handled separately
            default => null,
        };
    }
}
