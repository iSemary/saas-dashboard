<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\TimeEntry;
use Illuminate\Pagination\LengthAwarePaginator;

interface TimeEntryRepositoryInterface
{
    public function find(string $id): ?TimeEntry;
    public function findOrFail(string $id): TimeEntry;
    public function create(array $data): TimeEntry;
    public function update(string $id, array $data): TimeEntry;
    public function delete(string $id): bool;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function list(array $filters = []): array;
    public function count(array $filters = []): int;
    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection;
    public function sumDurationByUserAndDate(string $userId, string $date): int;
    public function getUtilization(string $userId, string $from, string $to): array;
    public function getAnomalies(int $limit = 50): array;
    public function getBillableRatio(string $userId, string $from, string $to): array;
}
