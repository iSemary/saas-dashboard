<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Modules\SmsMarketing\Domain\Entities\SmAbTest;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSmAbTestRepository implements SmAbTestRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?SmAbTest
    {
        return SmAbTest::find($id);
    }

    public function findOrFail(int $id): SmAbTest
    {
        return SmAbTest::findOrFail($id);
    }

    public function create(array $data): SmAbTest
    {
        return SmAbTest::create($data);
    }

    public function update(int $id, array $data): SmAbTest
    {
        $model = SmAbTest::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return SmAbTest::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SmAbTest::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SmAbTest::query();

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
        $query = SmAbTest::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['variant_name' => 'variant_name']);
        return $this->getResults($query, $params);
    }
}
