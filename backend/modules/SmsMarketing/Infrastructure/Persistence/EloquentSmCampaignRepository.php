<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Modules\SmsMarketing\Domain\Entities\SmCampaign;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSmCampaignRepository implements SmCampaignRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?SmCampaign
    {
        return SmCampaign::find($id);
    }

    public function findOrFail(int $id): SmCampaign
    {
        return SmCampaign::findOrFail($id);
    }

    public function create(array $data): SmCampaign
    {
        return SmCampaign::create($data);
    }

    public function update(int $id, array $data): SmCampaign
    {
        $model = SmCampaign::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return SmCampaign::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SmCampaign::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SmCampaign::query();

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
        $query = SmCampaign::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name']);
        return $this->getResults($query, $params);
    }

    public function list(array $filters = []): array
    {
        return SmCampaign::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['limit']), fn($q) => $q->limit($filters['limit']))
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return SmCampaign::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

    public function sum(string $column): float
    {
        return (float) SmCampaign::query()->sum($column);
    }

}
