<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Persistence;

use Modules\Accounting\Domain\Entities\JournalItem;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentJournalItemRepository implements JournalItemRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?JournalItem
    {
        return JournalItem::find($id);
    }

    public function findOrFail(int $id): JournalItem
    {
        return JournalItem::findOrFail($id);
    }

    public function create(array $data): JournalItem
    {
        return JournalItem::create($data);
    }

    public function update(int $id, array $data): JournalItem
    {
        $model = JournalItem::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return JournalItem::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return JournalItem::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = JournalItem::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return JournalItem::query()->orderBy('name')->get()->toArray();
    }

    public function count(array $filters = []): int
    {
        return JournalItem::query()->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = JournalItem::query()->select(['*']);
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
