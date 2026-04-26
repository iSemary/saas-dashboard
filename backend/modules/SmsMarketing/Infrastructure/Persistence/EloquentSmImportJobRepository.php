<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Modules\SmsMarketing\Domain\Entities\SmImportJob;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSmImportJobRepository implements SmImportJobRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?SmImportJob
    {
        return SmImportJob::find($id);
    }

    public function findOrFail(int $id): SmImportJob
    {
        return SmImportJob::findOrFail($id);
    }

    public function create(array $data): SmImportJob
    {
        return SmImportJob::create($data);
    }

    public function update(int $id, array $data): SmImportJob
    {
        $model = SmImportJob::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return SmImportJob::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SmImportJob::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SmImportJob::query();

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
        $query = SmImportJob::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['contact_list_id' => 'contact_list_id']);
        return $this->getResults($query, $params);
    }
}
