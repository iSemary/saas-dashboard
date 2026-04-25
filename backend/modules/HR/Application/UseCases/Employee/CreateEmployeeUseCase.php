<?php

namespace Modules\HR\Application\UseCases\Employee;

use Modules\HR\Application\DTOs\CreateEmployeeData;
use Modules\HR\Domain\Entities\Employee;
use Modules\HR\Infrastructure\Persistence\EmployeeRepositoryInterface;

class CreateEmployeeUseCase
{
    public function __construct(
        protected EmployeeRepositoryInterface $repository,
    ) {}

    public function execute(CreateEmployeeData $data): Employee
    {
        $arrayData = $data->toArray();
        $arrayData['created_by'] = auth()->id();
        return $this->repository->create($arrayData);
    }
}
