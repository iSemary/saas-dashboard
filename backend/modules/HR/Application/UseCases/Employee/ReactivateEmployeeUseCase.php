<?php

namespace Modules\HR\Application\UseCases\Employee;

use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;

class ReactivateEmployeeUseCase
{
    public function __construct(
        protected EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(int $employeeId, ?string $reason = null): Employee
    {
        $employee = $this->repository->findOrFail($employeeId);
        $employee->reactivate($reason);
        return $employee->fresh();
    }
}
