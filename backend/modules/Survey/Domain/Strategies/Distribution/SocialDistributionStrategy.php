<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Distribution;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyShare;

class SocialDistributionStrategy implements DistributionStrategyInterface
{
    public function getName(): string
    {
        return 'social';
    }

    public function getLabel(): string
    {
        return 'Social Media';
    }

    public function distribute(Survey $survey, SurveyShare $share, array $recipients): array
    {
        $url = $share->getPublicUrl();
        $platforms = $recipients['platforms'] ?? ['twitter', 'facebook', 'linkedin'];

        $results = [];

        foreach ($platforms as $platform) {
            $results[] = [
                'platform' => $platform,
                'share_url' => $this->getPlatformShareUrl($platform, $url, $survey->title),
                'status' => 'ready',
                'channel' => 'social',
            ];
        }

        return $results;
    }

    private function getPlatformShareUrl(string $platform, string $url, string $title): string
    {
        return match ($platform) {
            'twitter' => "https://twitter.com/intent/tweet?url={$url}&text=" . urlencode($title),
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$url}",
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$url}",
            default => $url,
        };
    }

    public function validateConfig(array $config): bool
    {
        return true;
    }
}
