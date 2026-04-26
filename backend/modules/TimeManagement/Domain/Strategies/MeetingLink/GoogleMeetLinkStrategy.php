<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\MeetingLink;

class GoogleMeetLinkStrategy implements MeetingLinkStrategyInterface
{
    public function getProviderName(): string
    {
        return 'google_meet';
    }

    public function generateLink(array $eventData): ?string
    {
        return null;
    }
}
