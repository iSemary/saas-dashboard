<?php

namespace Modules\HR\Application\UseCases\Employee;

use Modules\HR\Application\DTOs\UpdateEmployeeData;
use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;

class UpdateEmployeeUseCase
{
    public function __construct(
        protected EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(int $id, UpdateEmployeeData $data): Employee
    {
        return $this->repository->update($id, $data->toArray());
    }
}
