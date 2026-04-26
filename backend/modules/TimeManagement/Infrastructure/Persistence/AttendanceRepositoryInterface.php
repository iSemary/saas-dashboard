<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\Attendance;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttendanceRepositoryInterface
{
    public function find(string $id): ?Attendance;
    public function findOrFail(string $id): Attendance;
    public function create(array $data): Attendance;
    public function update(string $id, array $data): Attendance;
    public function delete(string $id): bool;
    public function paginateByUser(string $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findActiveClockIn(string $userId, string $date): ?Attendance;
}
