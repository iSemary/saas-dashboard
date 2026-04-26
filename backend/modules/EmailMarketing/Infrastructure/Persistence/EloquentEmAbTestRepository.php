<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Modules\EmailMarketing\Domain\Entities\EmAbTest;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEmAbTestRepository implements EmAbTestRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?EmAbTest
    {
        return EmAbTest::find($id);
    }

    public function findOrFail(int $id): EmAbTest
    {
        return EmAbTest::findOrFail($id);
    }

    public function create(array $data): EmAbTest
    {
        return EmAbTest::create($data);
    }

    public function update(int $id, array $data): EmAbTest
    {
        $model = EmAbTest::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return EmAbTest::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return EmAbTest::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EmAbTest::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->orWhere('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = EmAbTest::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['variant_name' => 'variant_name']);
        return $this->getResults($query, $params);
    }
}
