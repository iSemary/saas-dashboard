<?php

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\SmsMarketing\Domain\Entities\SmAutomationRule;

interface SmAutomationRuleRepositoryInterface
{
    public function find(int $id): ?SmAutomationRule;
    public function findOrFail(int $id): SmAutomationRule;
    public function create(array $data): SmAutomationRule;
    public function update(int $id, array $data): SmAutomationRule;
    public function delete(int $id): bool;
    public function bulkDelete(array $ids): int;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getTableList(array $params): LengthAwarePaginator|Collection;
}
