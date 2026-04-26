<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\OvertimeRequest;
use Illuminate\Pagination\LengthAwarePaginator;

interface OvertimeRequestRepositoryInterface
{
    public function find(string $id): ?OvertimeRequest;
    public function findOrFail(string $id): OvertimeRequest;
    public function create(array $data): OvertimeRequest;
    public function update(string $id, array $data): OvertimeRequest;
    public function delete(string $id): bool;
    public function paginateByUser(string $userId, int $perPage = 15): LengthAwarePaginator;
    public function getOvertimeSummary(string $from, string $to): \Illuminate\Support\Collection;
}
