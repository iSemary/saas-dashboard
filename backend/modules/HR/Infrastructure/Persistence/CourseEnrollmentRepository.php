<?php

namespace Modules\HR\Infrastructure\Persistence;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\HR\Domain\Entities\CourseEnrollment;

class CourseEnrollmentRepository implements CourseEnrollmentRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = CourseEnrollment::query();
        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(int $id): CourseEnrollment
    {
        return CourseEnrollment::findOrFail($id);
    }

    public function create(array $data): CourseEnrollment
    {
        return CourseEnrollment::create($data);
    }

    public function update(int $id, array $data): CourseEnrollment
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        return CourseEnrollment::destroy($id) > 0;
    }
}
