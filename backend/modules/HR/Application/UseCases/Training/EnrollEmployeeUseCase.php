<?php

namespace Modules\HR\Application\UseCases\Training;

use Modules\HR\Domain\Entities\CourseEnrollment;
use Modules\HR\Infrastructure\Persistence\CourseEnrollmentRepositoryInterface;

class EnrollEmployeeUseCase
{
    public function __construct(
        protected CourseEnrollmentRepositoryInterface $repository,
    ) {}

    public function execute(array $data): CourseEnrollment
    {
        $data['enrolled_at'] = $data['enrolled_at'] ?? now();
        $data['status'] = $data['status'] ?? 'enrolled';
        return $this->repository->create($data);
    }
}
