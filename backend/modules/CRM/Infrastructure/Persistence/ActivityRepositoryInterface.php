<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\Activity;

interface ActivityRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Activity;
    public function create(array $data): Activity;
    public function update(int $id, array $data): Activity;
    public function delete(int $id): bool;
    public function getByType(string $type): Collection;
    public function getByStatus(string $status): Collection;
    public function getAssignedTo(int $userId): Collection;
    public function getOverdue(): Collection;
    public function getUpcoming(int $days = 7): Collection;
    public function getForToday(): Collection;
    public function complete(int $id, ?string $outcome = null): Activity;
}
