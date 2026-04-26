<?php
declare(strict_types=1);
namespace Modules\Expenses\Infrastructure\Persistence;

use Modules\Expenses\Domain\Entities\ExpensePolicy;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentExpensePolicyRepository implements ExpensePolicyRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?ExpensePolicy
    {
        return ExpensePolicy::find($id);
    }

    public function findOrFail(int $id): ExpensePolicy
    {
        return ExpensePolicy::findOrFail($id);
    }

    public function create(array $data): ExpensePolicy
    {
        return ExpensePolicy::create($data);
    }

    public function update(int $id, array $data): ExpensePolicy
    {
        $model = ExpensePolicy::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return ExpensePolicy::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return ExpensePolicy::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ExpensePolicy::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return ExpensePolicy::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return ExpensePolicy::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = ExpensePolicy::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
