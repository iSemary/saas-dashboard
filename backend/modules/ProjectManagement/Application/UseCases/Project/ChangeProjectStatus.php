<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\UseCases\Project;

use Modules\ProjectManagement\Domain\ValueObjects\ProjectStatus;
use Modules\ProjectManagement\Domain\Entities\Project;
use Modules\ProjectManagement\Infrastructure\Persistence\ProjectRepositoryInterface;

class ChangeProjectStatus
{
    public function __construct(
        private ProjectRepositoryInterface $repository
    ) {}

    public function execute(string $projectId, ProjectStatus $newStatus): Project
    {
        $project = $this->repository->findOrFail($projectId);
        $project->transitionStatus($newStatus);
        return $project->fresh();
    }
}
