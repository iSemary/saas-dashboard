<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Modules\EmailMarketing\Domain\Entities\EmWebhook;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEmWebhookRepository implements EmWebhookRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?EmWebhook
    {
        return EmWebhook::find($id);
    }

    public function findOrFail(int $id): EmWebhook
    {
        return EmWebhook::findOrFail($id);
    }

    public function create(array $data): EmWebhook
    {
        return EmWebhook::create($data);
    }

    public function update(int $id, array $data): EmWebhook
    {
        $model = EmWebhook::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return EmWebhook::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return EmWebhook::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EmWebhook::query();

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
        $query = EmWebhook::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name', 'url' => 'url']);
        return $this->getResults($query, $params);
    }
}
