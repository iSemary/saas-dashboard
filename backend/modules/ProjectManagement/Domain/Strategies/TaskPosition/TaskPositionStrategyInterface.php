<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\TaskPosition;

interface TaskPositionStrategyInterface
{
    public function calculatePosition(string $columnId, ?string $beforeTaskId, ?string $afterTaskId): float;
}
