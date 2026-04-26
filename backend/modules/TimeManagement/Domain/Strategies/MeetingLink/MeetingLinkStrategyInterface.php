<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\MeetingLink;

interface MeetingLinkStrategyInterface
{
    public function getProviderName(): string;
    public function generateLink(array $eventData): ?string;
}
