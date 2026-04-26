<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\TaskPosition;

class DefaultTaskPositionStrategy implements TaskPositionStrategyInterface
{
    public function calculatePosition(string $columnId, ?string $beforeTaskId, ?string $afterTaskId): float
    {
        // Fractional indexing: place between before and after
        // Default: append to end
        if ($beforeTaskId === null && $afterTaskId === null) {
            return 65536.0; // 2^16 as initial position
        }

        if ($beforeTaskId === null) {
            return 32768.0; // Place at start
        }

        if ($afterTaskId === null) {
            return 98304.0; // Place at end
        }

        // In a real implementation, we'd look up positions and average them
        return 65536.0;
    }
}
