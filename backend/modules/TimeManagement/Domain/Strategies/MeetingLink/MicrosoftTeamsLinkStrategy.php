<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\MeetingLink;

class MicrosoftTeamsLinkStrategy implements MeetingLinkStrategyInterface
{
    public function getProviderName(): string
    {
        return 'microsoft_teams';
    }

    public function generateLink(array $eventData): ?string
    {
        return null;
    }
}
