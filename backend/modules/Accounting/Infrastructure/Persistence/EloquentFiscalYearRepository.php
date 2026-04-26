<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\FiscalYear;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentFiscalYearRepository implements FiscalYearRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?FiscalYear
    {
        return FiscalYear::find($id);
    }

    public function findOrFail(int $id): FiscalYear
    {
        return FiscalYear::findOrFail($id);
    }

    public function create(array $data): FiscalYear
    {
        return FiscalYear::create($data);
    }

    public function update(int $id, array $data): FiscalYear
    {
        $model = FiscalYear::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return FiscalYear::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return FiscalYear::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = FiscalYear::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return FiscalYear::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return FiscalYear::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = FiscalYear::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
