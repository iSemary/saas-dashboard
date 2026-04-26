<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Modules\EmailMarketing\Domain\Entities\EmCampaign;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEmCampaignRepository implements EmCampaignRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?EmCampaign
    {
        return EmCampaign::find($id);
    }

    public function findOrFail(int $id): EmCampaign
    {
        return EmCampaign::findOrFail($id);
    }

    public function create(array $data): EmCampaign
    {
        return EmCampaign::create($data);
    }

    public function update(int $id, array $data): EmCampaign
    {
        $model = EmCampaign::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return EmCampaign::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return EmCampaign::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EmCampaign::query();

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
        $query = EmCampaign::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['name' => 'name', 'subject' => 'subject']);
        return $this->getResults($query, $params);
    }

    public function list(array $filters = []): array
    {
        return EmCampaign::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['limit']), fn($q) => $q->limit($filters['limit']))
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return EmCampaign::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }
}
