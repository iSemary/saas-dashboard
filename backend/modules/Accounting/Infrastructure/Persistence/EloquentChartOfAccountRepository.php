<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\ChartOfAccount;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentChartOfAccountRepository implements ChartOfAccountRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?ChartOfAccount
    {
        return ChartOfAccount::with(['parent', 'creator'])->find($id);
    }

    public function findOrFail(int $id): ChartOfAccount
    {
        return ChartOfAccount::with(['parent', 'children', 'creator'])->findOrFail($id);
    }

    public function create(array $data): ChartOfAccount
    {
        return ChartOfAccount::create($data);
    }

    public function update(int $id, array $data): ChartOfAccount
    {
        $model = ChartOfAccount::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return ChartOfAccount::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return ChartOfAccount::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ChartOfAccount::with(['parent', 'creator']);

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('code', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('code')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        $query = ChartOfAccount::query();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->active()->orderBy('code')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        $query = ChartOfAccount::query();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['is_active'])) {
            $query->where('is_active', true);
        }

        return $query->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = ChartOfAccount::query()
            ->with(['parent', 'creator'])
            ->select([
                'id', 'code', 'name', 'type', 'sub_type', 'parent_id',
                'is_active', 'is_leaf', 'current_balance', 'created_by', 'created_at',
            ]);

        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }

    public function getActiveGroupedByType(): \Illuminate\Support\Collection
    {
        return ChartOfAccount::where('is_active', true)
            ->select(['id', 'code', 'name', 'type', 'current_balance'])
            ->orderBy('code')
            ->get()
            ->groupBy('type');
    }
}
