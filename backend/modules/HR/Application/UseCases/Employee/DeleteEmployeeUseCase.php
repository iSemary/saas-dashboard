<?php

namespace Modules\HR\Application\UseCases\Employee;

use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;

class DeleteEmployeeUseCase
{
    public function __construct(
        protected EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
