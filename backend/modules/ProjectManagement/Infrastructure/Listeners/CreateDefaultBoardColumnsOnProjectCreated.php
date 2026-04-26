<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Listeners;

use Modules\ProjectManagement\Domain\Events\ProjectCreated;
use Modules\ProjectManagement\Domain\Strategies\BoardColumn\BoardColumnStrategyInterface;
use Modules\ProjectManagement\Domain\Entities\BoardColumn;

class CreateDefaultBoardColumnsOnProjectCreated
{
    public function __construct(
        private BoardColumnStrategyInterface $boardColumnStrategy
    ) {}

    public function handle(ProjectCreated $event): void
    {
        $project = $event->project;
        $defaultColumns = $this->boardColumnStrategy->getDefaultColumns();

        foreach ($defaultColumns as $columnData) {
            BoardColumn::create([
                'tenant_id' => $project->tenant_id,
                'project_id' => $project->id,
                'name' => $columnData['name'],
                'type' => $columnData['type'],
                'status_mapping' => $columnData['status_mapping'],
                'position' => $columnData['position'],
            ]);
        }
    }
}
