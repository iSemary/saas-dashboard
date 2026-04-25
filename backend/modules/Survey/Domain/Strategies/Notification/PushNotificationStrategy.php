<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Notification;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyResponse;

class PushNotificationStrategy implements NotificationStrategyInterface
{
    public function getChannel(): string
    {
        return 'push';
    }

    public function getLabel(): string
    {
        return 'Push Notification';
    }

    public function supports(string $channel): bool
    {
        return $channel === 'push';
    }

    public function send(Survey $survey, SurveyResponse $response, array $config): array
    {
        // Integration with Notification module would go here
        // $notificationService->push($config['user_id'], $config['title'], $config['body']);

        return [
            'success' => true,
            'message' => 'Push notification queued',
            'channel' => 'push',
        ];
    }

    public function getPreview(Survey $survey, array $config): array
    {
        return [
            'channel' => 'push',
            'preview' => 'Push notification: ' . ($config['title'] ?? 'Survey Update'),
        ];
    }

    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'user_id',
                'type' => 'number',
                'required' => true,
                'label' => 'User ID',
            ],
            [
                'name' => 'title',
                'type' => 'text',
                'required' => true,
                'label' => 'Notification Title',
            ],
            [
                'name' => 'body',
                'type' => 'textarea',
                'required' => true,
                'label' => 'Notification Body',
            ],
        ];
    }

    public function validateConfig(array $config): bool
    {
        return isset($config['user_id']) && isset($config['title']);
    }
}
