<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Modules\SmsMarketing\Domain\Entities\SmWebhook;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSmWebhookRepository implements SmWebhookRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?SmWebhook
    {
        return SmWebhook::find($id);
    }

    public function findOrFail(int $id): SmWebhook
    {
        return SmWebhook::findOrFail($id);
    }

    public function create(array $data): SmWebhook
    {
        return SmWebhook::create($data);
    }

    public function update(int $id, array $data): SmWebhook
    {
        $model = SmWebhook::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return SmWebhook::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SmWebhook::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SmWebhook::query();

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
        $query = SmWebhook::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name', 'url' => 'url']);
        return $this->getResults($query, $params);
    }
}
