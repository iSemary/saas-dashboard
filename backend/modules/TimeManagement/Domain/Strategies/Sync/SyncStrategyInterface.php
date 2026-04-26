<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\Sync;

interface SyncStrategyInterface
{
    public function sync(string $provider, string $userId, array $options = []): array;
}
