<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Modules\SmsMarketing\Domain\Entities\SmAutomationRule;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSmAutomationRuleRepository implements SmAutomationRuleRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?SmAutomationRule
    {
        return SmAutomationRule::find($id);
    }

    public function findOrFail(int $id): SmAutomationRule
    {
        return SmAutomationRule::findOrFail($id);
    }

    public function create(array $data): SmAutomationRule
    {
        return SmAutomationRule::create($data);
    }

    public function update(int $id, array $data): SmAutomationRule
    {
        $model = SmAutomationRule::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return SmAutomationRule::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SmAutomationRule::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SmAutomationRule::query();

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
        $query = SmAutomationRule::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name', 'trigger_type' => 'trigger_type']);
        return $this->getResults($query, $params);
    }
}
