<?php

namespace Modules\HR\Application\UseCases\Employee;

use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;

class TerminateEmployeeUseCase
{
    public function __construct(
        protected EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(int $employeeId, ?string $reason = null, ?\DateTimeInterface $terminationDate = null): Employee
    {
        $employee = $this->repository->findOrFail($employeeId);
        $employee->terminate($reason, $terminationDate);
        return $employee->fresh();
    }
}
