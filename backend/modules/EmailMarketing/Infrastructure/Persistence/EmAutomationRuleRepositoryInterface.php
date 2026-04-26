<?php

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\EmailMarketing\Domain\Entities\EmAutomationRule;

interface EmAutomationRuleRepositoryInterface
{
    public function find(int $id): ?EmAutomationRule;
    public function findOrFail(int $id): EmAutomationRule;
    public function create(array $data): EmAutomationRule;
    public function update(int $id, array $data): EmAutomationRule;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
