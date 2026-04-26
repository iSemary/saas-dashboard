<?php
declare(strict_types=1);
namespace Modules\Expenses\Infrastructure\Persistence;

use Modules\Expenses\Domain\Entities\ExpenseReport;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentExpenseReportRepository implements ExpenseReportRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?ExpenseReport
    {
        return ExpenseReport::find($id);
    }

    public function findOrFail(int $id): ExpenseReport
    {
        return ExpenseReport::findOrFail($id);
    }

    public function create(array $data): ExpenseReport
    {
        return ExpenseReport::create($data);
    }

    public function update(int $id, array $data): ExpenseReport
    {
        $model = ExpenseReport::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return ExpenseReport::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return ExpenseReport::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ExpenseReport::query();

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
        return ExpenseReport::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return ExpenseReport::query()
            ->when(!empty($filters['created_by']), fn($q) => $q->where('created_by', $filters['created_by']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = ExpenseReport::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
