<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\UseCases\Project;

use Modules\ProjectManagement\Application\DTOs\UpdateProjectData;
use Modules\ProjectManagement\Domain\Entities\Project;
use Modules\ProjectManagement\Infrastructure\Persistence\ProjectRepositoryInterface;

class UpdateProject
{
    public function __construct(
        private ProjectRepositoryInterface $repository
    ) {}

    public function execute(string $projectId, UpdateProjectData $data): Project
    {
        return $this->repository->update($projectId, $data->toArray());
    }
}
