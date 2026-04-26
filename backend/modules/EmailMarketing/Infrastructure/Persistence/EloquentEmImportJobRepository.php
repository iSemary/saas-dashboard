<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Modules\EmailMarketing\Domain\Entities\EmImportJob;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEmImportJobRepository implements EmImportJobRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?EmImportJob
    {
        return EmImportJob::find($id);
    }

    public function findOrFail(int $id): EmImportJob
    {
        return EmImportJob::findOrFail($id);
    }

    public function create(array $data): EmImportJob
    {
        return EmImportJob::create($data);
    }

    public function update(int $id, array $data): EmImportJob
    {
        $model = EmImportJob::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return EmImportJob::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return EmImportJob::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EmImportJob::query();

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
        $query = EmImportJob::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['contact_list_id' => 'contact_list_id']);
        return $this->getResults($query, $params);
    }
}
