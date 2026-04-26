<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Modules\EmailMarketing\Domain\Entities\EmContactList;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEmContactListRepository implements EmContactListRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?EmContactList
    {
        return EmContactList::find($id);
    }

    public function findOrFail(int $id): EmContactList
    {
        return EmContactList::findOrFail($id);
    }

    public function create(array $data): EmContactList
    {
        return EmContactList::create($data);
    }

    public function update(int $id, array $data): EmContactList
    {
        $model = EmContactList::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return EmContactList::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return EmContactList::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EmContactList::query();

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
        $query = EmContactList::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name']);
        return $this->getResults($query, $params);
    }

    public function list(array $filters = []): array
    {
        return EmContactList::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['limit']), fn($q) => $q->limit($filters['limit']))
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return EmContactList::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

}
