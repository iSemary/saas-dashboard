<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Distribution;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyShare;

class EmailDistributionStrategy implements DistributionStrategyInterface
{
    public function getName(): string
    {
        return 'email';
    }

    public function getLabel(): string
    {
        return 'Email';
    }

    public function distribute(Survey $survey, SurveyShare $share, array $recipients): array
    {
        // Integration with Email module would go here
        $results = [];

        foreach ($recipients as $recipient) {
            // Example: Send email via Email module
            // $emailService->sendSurveyInvitation($recipient, $survey, $share);

            $results[] = [
                'recipient' => $recipient,
                'status' => 'queued',
                'channel' => 'email',
            ];
        }

        return $results;
    }

    public function validateConfig(array $config): bool
    {
        return isset($config['recipients']) && is_array($config['recipients']);
    }
}
