<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\CourseEnrollment;

interface CourseEnrollmentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): CourseEnrollment;
    public function create(array $data): CourseEnrollment;
    public function update(int $id, array $data): CourseEnrollment;
    public function delete(int $id): bool;
}
