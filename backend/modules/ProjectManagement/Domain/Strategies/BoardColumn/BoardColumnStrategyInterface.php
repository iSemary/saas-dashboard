<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\BoardColumn;

interface BoardColumnStrategyInterface
{
    public function enforceWipLimit(string $columnId, int $currentCount, ?int $wipLimit): bool;
    public function getDefaultColumns(): array;
}
