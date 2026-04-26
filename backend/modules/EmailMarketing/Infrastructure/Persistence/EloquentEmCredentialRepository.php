<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Modules\EmailMarketing\Domain\Entities\EmCredential;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEmCredentialRepository implements EmCredentialRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?EmCredential
    {
        return EmCredential::find($id);
    }

    public function findOrFail(int $id): EmCredential
    {
        return EmCredential::findOrFail($id);
    }

    public function create(array $data): EmCredential
    {
        return EmCredential::create($data);
    }

    public function update(int $id, array $data): EmCredential
    {
        $model = EmCredential::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return EmCredential::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return EmCredential::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EmCredential::query();

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
        $query = EmCredential::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name', 'provider' => 'provider']);
        return $this->getResults($query, $params);
    }
}
