<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Distribution;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyShare;

class EmbedDistributionStrategy implements DistributionStrategyInterface
{
    public function getName(): string
    {
        return 'embed';
    }

    public function getLabel(): string
    {
        return 'Embed (iframe)';
    }

    public function distribute(Survey $survey, SurveyShare $share, array $recipients): array
    {
        $embedUrl = $share->getEmbedUrl();
        $embedCode = $share->getEmbedCode();

        return [
            [
                'embed_url' => $embedUrl,
                'embed_code' => $embedCode,
                'status' => 'ready',
                'channel' => 'embed',
            ],
        ];
    }

    public function validateConfig(array $config): bool
    {
        return isset($config['width']) && isset($config['height']);
    }
}
