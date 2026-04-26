<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\Reconciliation;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentReconciliationRepository implements ReconciliationRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?Reconciliation
    {
        return Reconciliation::find($id);
    }

    public function findOrFail(int $id): Reconciliation
    {
        return Reconciliation::findOrFail($id);
    }

    public function create(array $data): Reconciliation
    {
        return Reconciliation::create($data);
    }

    public function update(int $id, array $data): Reconciliation
    {
        $model = Reconciliation::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return Reconciliation::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return Reconciliation::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Reconciliation::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return Reconciliation::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return Reconciliation::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = Reconciliation::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
