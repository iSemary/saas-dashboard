<?php
declare(strict_types=1);
namespace Modules\Expenses\Infrastructure\Persistence;

use Modules\Expenses\Domain\Entities\Reimbursement;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentReimbursementRepository implements ReimbursementRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?Reimbursement
    {
        return Reimbursement::find($id);
    }

    public function findOrFail(int $id): Reimbursement
    {
        return Reimbursement::findOrFail($id);
    }

    public function create(array $data): Reimbursement
    {
        return Reimbursement::create($data);
    }

    public function update(int $id, array $data): Reimbursement
    {
        $model = Reimbursement::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return Reimbursement::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return Reimbursement::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Reimbursement::query();

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
        return Reimbursement::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return Reimbursement::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = Reimbursement::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }

    public function sumByCreatorAndStatus(int $userId, string $status, string $column): float
    {
        return (float) Reimbursement::where('created_by', $userId)
            ->where('status', $status)
            ->sum($column);
    }
}
