<?php

namespace Modules\HR\Application\UseCases\Department;

use Modules\HR\Application\DTOs\UpdateDepartmentData;
use Modules\HR\Domain\Entities\Department;
use Modules\HR\Infrastructure\Persistence\DepartmentRepositoryInterface;

class UpdateDepartmentUseCase
{
    public function __construct(
        protected DepartmentRepositoryInterface $repository,
    ) {}

    public function execute(int $id, UpdateDepartmentData $data): Department
    {
        return $this->repository->update($id, $data->toArray());
    }
}
