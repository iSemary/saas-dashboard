<?php

namespace Modules\HR\Application\UseCases\Employee;

use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;

class TransferEmployeeUseCase
{
    public function __construct(
        protected EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(int $employeeId, int $newDepartmentId, ?int $newPositionId = null, ?string $reason = null): Employee
    {
        $employee = $this->repository->findOrFail($employeeId);
        $employee->transfer($newDepartmentId, $newPositionId, $reason);
        return $employee->fresh();
    }
}
