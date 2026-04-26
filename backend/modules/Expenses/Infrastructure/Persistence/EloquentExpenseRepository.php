<?php
declare(strict_types=1);
namespace Modules\Expenses\Infrastructure\Persistence;

use Modules\Expenses\Domain\Entities\Expense;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentExpenseRepository implements ExpenseRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?Expense
    {
        return Expense::find($id);
    }

    public function findOrFail(int $id): Expense
    {
        return Expense::findOrFail($id);
    }

    public function create(array $data): Expense
    {
        return Expense::create($data);
    }

    public function update(int $id, array $data): Expense
    {
        $model = Expense::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return Expense::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return Expense::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Expense::query();

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
        return Expense::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return Expense::query()
            ->when(!empty($filters['created_by']), fn($q) => $q->where('created_by', $filters['created_by']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = Expense::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }

    public function sumByCreator(int $userId, string $column): float
    {
        return (float) Expense::where('created_by', $userId)->sum($column);
    }

    public function sumByCreatorAndStatus(int $userId, string $status, string $column): float
    {
        return (float) Expense::where('created_by', $userId)
            ->where('status', $status)
            ->sum($column);
    }
}
