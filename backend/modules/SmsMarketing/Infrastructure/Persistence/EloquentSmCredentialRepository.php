<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Modules\SmsMarketing\Domain\Entities\SmCredential;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSmCredentialRepository implements SmCredentialRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?SmCredential
    {
        return SmCredential::find($id);
    }

    public function findOrFail(int $id): SmCredential
    {
        return SmCredential::findOrFail($id);
    }

    public function create(array $data): SmCredential
    {
        return SmCredential::create($data);
    }

    public function update(int $id, array $data): SmCredential
    {
        $model = SmCredential::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return SmCredential::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SmCredential::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SmCredential::query();

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
        $query = SmCredential::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name', 'provider' => 'provider']);
        return $this->getResults($query, $params);
    }
}
