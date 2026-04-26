<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\Budget;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentBudgetRepository implements BudgetRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?Budget
    {
        return Budget::find($id);
    }

    public function findOrFail(int $id): Budget
    {
        return Budget::findOrFail($id);
    }

    public function create(array $data): Budget
    {
        return Budget::create($data);
    }

    public function update(int $id, array $data): Budget
    {
        $model = Budget::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return Budget::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return Budget::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Budget::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return Budget::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return Budget::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = Budget::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
