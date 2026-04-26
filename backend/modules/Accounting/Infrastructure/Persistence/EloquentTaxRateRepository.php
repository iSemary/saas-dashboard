<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\TaxRate;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentTaxRateRepository implements TaxRateRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?TaxRate
    {
        return TaxRate::find($id);
    }

    public function findOrFail(int $id): TaxRate
    {
        return TaxRate::findOrFail($id);
    }

    public function create(array $data): TaxRate
    {
        return TaxRate::create($data);
    }

    public function update(int $id, array $data): TaxRate
    {
        $model = TaxRate::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return TaxRate::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return TaxRate::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TaxRate::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return TaxRate::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return TaxRate::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = TaxRate::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
