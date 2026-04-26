<?php
declare(strict_types=1);
namespace Modules\Expenses\Infrastructure\Persistence;

use Modules\Expenses\Domain\Entities\ExpenseCategory;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentExpenseCategoryRepository implements ExpenseCategoryRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?ExpenseCategory
    {
        return ExpenseCategory::find($id);
    }

    public function findOrFail(int $id): ExpenseCategory
    {
        return ExpenseCategory::findOrFail($id);
    }

    public function create(array $data): ExpenseCategory
    {
        return ExpenseCategory::create($data);
    }

    public function update(int $id, array $data): ExpenseCategory
    {
        $model = ExpenseCategory::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return ExpenseCategory::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return ExpenseCategory::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ExpenseCategory::query();

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
        return ExpenseCategory::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return ExpenseCategory::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = ExpenseCategory::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
