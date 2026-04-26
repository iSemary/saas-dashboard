<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Modules\SmsMarketing\Domain\Entities\SmContactList;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSmContactListRepository implements SmContactListRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?SmContactList
    {
        return SmContactList::find($id);
    }

    public function findOrFail(int $id): SmContactList
    {
        return SmContactList::findOrFail($id);
    }

    public function create(array $data): SmContactList
    {
        return SmContactList::create($data);
    }

    public function update(int $id, array $data): SmContactList
    {
        $model = SmContactList::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return SmContactList::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SmContactList::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SmContactList::query();

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
        $query = SmContactList::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name']);
        return $this->getResults($query, $params);
    }

    public function list(array $filters = []): array
    {
        return SmContactList::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['limit']), fn($q) => $q->limit($filters['limit']))
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return SmContactList::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

    public function sum(string $column): float
    {
        return (float) SmContactList::query()->sum($column);
    }

}
