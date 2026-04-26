<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\Timesheet;
use Illuminate\Pagination\LengthAwarePaginator;

interface TimesheetRepositoryInterface
{
    public function find(string $id): ?Timesheet;
    public function findOrFail(string $id): Timesheet;
    public function create(array $data): Timesheet;
    public function update(string $id, array $data): Timesheet;
    public function delete(string $id): bool;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
    public function getSubmittedHoursSummary(string $from, string $to): \Illuminate\Support\Collection;
}
