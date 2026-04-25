<?php

namespace Modules\HR\Application\UseCases\Department;

use Modules\HR\Application\DTOs\CreateDepartmentData;
use Modules\HR\Domain\Entities\Department;
use Modules\HR\Infrastructure\Persistence\DepartmentRepositoryInterface;

class CreateDepartmentUseCase
{
    public function __construct(
        protected DepartmentRepositoryInterface $repository,
    ) {}

    public function execute(CreateDepartmentData $data): Department
    {
        $arrayData = $data->toArray();
        $arrayData['created_by'] = auth()->id();
        return $this->repository->create($arrayData);
    }
}
