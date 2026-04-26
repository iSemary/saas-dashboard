<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Notification;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyResponse;

class SmsNotificationStrategy implements NotificationStrategyInterface
{
    public function getChannel(): string
    {
        return 'sms';
    }

    public function getLabel(): string
    {
        return 'SMS Notification';
    }

    public function supports(string $channel): bool
    {
        return $channel === 'sms';
    }

    public function send(Survey $survey, SurveyResponse $response, array $config): array
    {
        // Integration with SMS service would go here
        // $smsService->send($config['phone_number'], $config['message']);

        return [
            'success' => true,
            'message' => translate('message.action_completed'),
            'channel' => 'sms',
        ];
    }

    public function getPreview(Survey $survey, array $config): array
    {
        return [
            'channel' => 'sms',
            'preview' => 'SMS will be sent to: ' . ($config['phone_number'] ?? 'respondent'),
        ];
    }

    public function getConfigFields(): array
    {
        return [
            [
                'name' => 'phone_number',
                'type' => 'tel',
                'required' => true,
                'label' => 'Phone Number',
            ],
            [
                'name' => 'message',
                'type' => 'textarea',
                'required' => true,
                'label' => 'Message Content',
            ],
        ];
    }

    public function validateConfig(array $config): bool
    {
        return isset($config['phone_number']) && !empty($config['phone_number']);
    }
}
