<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Distribution;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyShare;

class LinkDistributionStrategy implements DistributionStrategyInterface
{
    public function getName(): string
    {
        return 'link';
    }

    public function getLabel(): string
    {
        return 'Direct Link';
    }

    public function distribute(Survey $survey, SurveyShare $share, array $recipients): array
    {
        // Link distribution doesn't require sending - just returns the link
        return [
            [
                'url' => $share->getPublicUrl(),
                'status' => 'ready',
                'channel' => 'link',
            ],
        ];
    }

    public function validateConfig(array $config): bool
    {
        // Link distribution requires minimal config
        return true;
    }
}
