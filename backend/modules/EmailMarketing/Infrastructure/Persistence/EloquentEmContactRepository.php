<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Modules\EmailMarketing\Domain\Entities\EmContact;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEmContactRepository implements EmContactRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?EmContact
    {
        return EmContact::find($id);
    }

    public function findOrFail(int $id): EmContact
    {
        return EmContact::findOrFail($id);
    }

    public function create(array $data): EmContact
    {
        return EmContact::create($data);
    }

    public function update(int $id, array $data): EmContact
    {
        $model = EmContact::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return EmContact::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return EmContact::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EmContact::query();

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
        $query = EmContact::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['email' => 'email', 'first_name' => 'first_name', 'last_name' => 'last_name']);
        return $this->getResults($query, $params);
    }

    public function list(array $filters = []): array
    {
        return EmContact::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['limit']), fn($q) => $q->limit($filters['limit']))
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return EmContact::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

}
