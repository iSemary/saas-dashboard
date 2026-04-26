<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Infrastructure\Persistence;

use Modules\EmailMarketing\Domain\Entities\EmSendingLog;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEmSendingLogRepository implements EmSendingLogRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?EmSendingLog
    {
        return EmSendingLog::find($id);
    }

    public function findOrFail(int $id): EmSendingLog
    {
        return EmSendingLog::findOrFail($id);
    }

    public function create(array $data): EmSendingLog
    {
        return EmSendingLog::create($data);
    }

    public function update(int $id, array $data): EmSendingLog
    {
        $model = EmSendingLog::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return EmSendingLog::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return EmSendingLog::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = EmSendingLog::query();

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
        $query = EmSendingLog::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['campaign_id' => 'campaign_id', 'contact_id' => 'contact_id']);
        return $this->getResults($query, $params);
    }

    public function list(array $filters = []): array
    {
        return EmSendingLog::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['limit']), fn($q) => $q->limit($filters['limit']))
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return EmSendingLog::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

}
