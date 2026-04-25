<?php

namespace Modules\HR\Application\UseCases\Department;

use Modules\HR\Infrastructure\Persistence\DepartmentRepositoryInterface;

class DeleteDepartmentUseCase
{
    public function __construct(
        protected DepartmentRepositoryInterface $repository,
    ) {}

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
