<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Strategies\BoardColumn;

class DefaultBoardColumnStrategy implements BoardColumnStrategyInterface
{
    public function enforceWipLimit(string $columnId, int $currentCount, ?int $wipLimit): bool
    {
        if ($wipLimit === null) {
            return true;
        }

        return $currentCount < $wipLimit;
    }

    public function getDefaultColumns(): array
    {
        return [
            ['name' => 'To Do', 'type' => 'todo', 'status_mapping' => 'todo', 'position' => 1],
            ['name' => 'In Progress', 'type' => 'in_progress', 'status_mapping' => 'in_progress', 'position' => 2],
            ['name' => 'In Review', 'type' => 'in_review', 'status_mapping' => 'in_review', 'position' => 3],
            ['name' => 'Done', 'type' => 'done', 'status_mapping' => 'done', 'position' => 4],
        ];
    }
}
