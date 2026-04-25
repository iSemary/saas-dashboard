<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Strategies\Distribution;

use Modules\Survey\Domain\Entities\Survey;
use Modules\Survey\Domain\Entities\SurveyShare;

interface DistributionStrategyInterface
{
    /**
     * Check if this strategy supports the given channel.
     */
    public function supports(string $channel): bool;

    /**
     * Distribute the survey via this channel.
     *
     * @return array{success: bool, message: string, recipients_count?: int}
     */
    public function distribute(Survey $survey, SurveyShare $share, array $recipients = []): array;

    /**
     * Get preview/config for this distribution method.
     */
    public function getPreview(Survey $survey, SurveyShare $share): array;

    /**
     * Track the distribution (update uses_count, etc.)
     */
    public function trackDistribution(SurveyShare $share): void;

    /**
     * Get the channel name.
     */
    public function getChannel(): string;

    /**
     * Get the channel label.
     */
    public function getLabel(): string;

    /**
     * Get required configuration fields.
     *
     * @return array<array{name: string, type: string, required: bool, label: string}>
     */
    public function getConfigFields(): array;
}
