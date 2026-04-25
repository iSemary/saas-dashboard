<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Notification;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyResponse;

class EmailNotificationStrategy implements NotificationStrategyInterface
{
    public function getChannel(): string
    {
        return 'email';
    }

    public function getLabel(): string
    {
        return 'Email Notification';
    }

    public function supports(string $channel): bool
    {
        return $channel === 'email';
    }

    public function send(Survey $survey, SurveyResponse $response, array $config): array
    {
        // Integration with Email module would go here
        // $emailService->send($config['recipient'], $config['template'], $data);

        return [
            'success' => true,
            'message' => 'Email notification queued',
            'channel' => 'email',
        ];
    }

    public function getPreview(Survey $survey, array $config): array
    {
        return [
            'channel' => 'email',
            'preview' => 'Email will be sent to: ' . ($config['recipient'] ?? 'respondent'),
        ];
    }

    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'recipient',
                'type' => 'email',
                'required' => true,
                'label' => 'Recipient Email',
            ],
            [
                'name' => 'template',
                'type' => 'select',
                'required' => true,
                'label' => 'Email Template',
                'options' => ['survey_completed', 'survey_response', 'custom'],
            ],
            [
                'name' => 'subject',
                'type' => 'text',
                'required' => false,
                'label' => 'Custom Subject',
            ],
        ];
    }

    public function validateConfig(array $config): bool
    {
        return isset($config['recipient']) && filter_var($config['recipient'], FILTER_VALIDATE_EMAIL);
    }
}
