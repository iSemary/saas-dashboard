<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Distribution;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyShare;

class SmsDistributionStrategy implements DistributionStrategyInterface
{
    public function getName(): string
    {
        return 'sms';
    }

    public function getLabel(): string
    {
        return 'SMS';
    }

    public function distribute(Survey $survey, SurveyShare $share, array $recipients): array
    {
        $results = [];
        $shortUrl = $share->getPublicUrl();

        foreach ($recipients as $recipient) {
            // Integration with SMS service would go here
            // $smsService->send($recipient, "Take our survey: {$shortUrl}");

            $results[] = [
                'recipient' => $recipient,
                'status' => 'queued',
                'channel' => 'sms',
            ];
        }

        return $results;
    }

    public function validateConfig(array $config): bool
    {
        return isset($config['phone_numbers']) && is_array($config['phone_numbers']);
    }
}
