<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Application\UseCases\Project;

use Modules\ProjectManagement\Application\DTOs\CreateProjectData;
use Modules\ProjectManagement\Domain\Entities\Project;
use Modules\ProjectManagement\Infrastructure\Persistence\ProjectRepositoryInterface;

class CreateProject
{
    public function __construct(
        private ProjectRepositoryInterface $repository
    ) {}

    public function execute(CreateProjectData $data, string $userId): Project
    {
        $projectData = $data->toArray();
        $projectData['created_by'] = $userId;

        return $this->repository->create($projectData);
    }
}
