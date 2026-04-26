<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Modules\EmailMarketing\Domain\Entities\EmAutomationRule;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEmAutomationRuleRepository implements EmAutomationRuleRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?EmAutomationRule
    {
        return EmAutomationRule::find($id);
    }

    public function findOrFail(int $id): EmAutomationRule
    {
        return EmAutomationRule::findOrFail($id);
    }

    public function create(array $data): EmAutomationRule
    {
        return EmAutomationRule::create($data);
    }

    public function update(int $id, array $data): EmAutomationRule
    {
        $model = EmAutomationRule::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return EmAutomationRule::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return EmAutomationRule::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EmAutomationRule::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->orWhere('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = EmAutomationRule::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name', 'trigger_type' => 'trigger_type']);
        return $this->getResults($query, $params);
    }
}
