<?php

namespace Modules\HR\Application\UseCases\Employee;

use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;

class PromoteEmployeeUseCase
{
    public function __construct(
        protected EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(int $employeeId, int $newPositionId, ?float $newSalary = null, ?string $reason = null): Employee
    {
        $employee = $this->repository->findOrFail($employeeId);
        $employee->promote($newPositionId, $newSalary, $reason);
        return $employee->fresh();
    }
}
