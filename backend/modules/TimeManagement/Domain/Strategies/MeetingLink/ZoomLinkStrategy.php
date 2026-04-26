<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\MeetingLink;

class ZoomLinkStrategy implements MeetingLinkStrategyInterface
{
    public function getProviderName(): string
    {
        return 'zoom';
    }

    public function generateLink(array $eventData): ?string
    {
        return null;
    }
}
