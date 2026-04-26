<?php
declare(strict_types=1);
namespace Modules\Expenses\Infrastructure\Persistence;

use Modules\Expenses\Domain\Entities\ExpenseTag;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentExpenseTagRepository implements ExpenseTagRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?ExpenseTag
    {
        return ExpenseTag::find($id);
    }

    public function findOrFail(int $id): ExpenseTag
    {
        return ExpenseTag::findOrFail($id);
    }

    public function create(array $data): ExpenseTag
    {
        return ExpenseTag::create($data);
    }

    public function update(int $id, array $data): ExpenseTag
    {
        $model = ExpenseTag::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return ExpenseTag::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return ExpenseTag::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ExpenseTag::query();

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
        return ExpenseTag::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return ExpenseTag::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = ExpenseTag::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
