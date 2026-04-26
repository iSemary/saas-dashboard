<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\TimeSession;
use Illuminate\Pagination\LengthAwarePaginator;

interface TimeSessionRepositoryInterface
{
    public function find(string $id): ?TimeSession;
    public function findOrFail(string $id): TimeSession;
    public function create(array $data): TimeSession;
    public function delete(string $id): bool;
    public function paginateByUser(string $userId, int $perPage = 15): LengthAwarePaginator;
    public function findActiveByUser(string $userId): ?TimeSession;
}
