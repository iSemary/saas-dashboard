<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Strategies\Sync;

class DefaultSyncStrategy implements SyncStrategyInterface
{
    public function sync(string $provider, string $userId, array $options = []): array
    {
        return ['synced' => 0, 'errors' => []];
    }
}
