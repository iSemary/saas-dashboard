<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\BudgetItem;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentBudgetItemRepository implements BudgetItemRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?BudgetItem
    {
        return BudgetItem::find($id);
    }

    public function findOrFail(int $id): BudgetItem
    {
        return BudgetItem::findOrFail($id);
    }

    public function create(array $data): BudgetItem
    {
        return BudgetItem::create($data);
    }

    public function update(int $id, array $data): BudgetItem
    {
        $model = BudgetItem::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return BudgetItem::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return BudgetItem::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BudgetItem::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return BudgetItem::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return BudgetItem::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = BudgetItem::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
