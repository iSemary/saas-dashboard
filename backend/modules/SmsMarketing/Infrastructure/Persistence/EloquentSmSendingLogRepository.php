<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Infrastructure\Persistence;

use Modules\SmsMarketing\Domain\Entities\SmSendingLog;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSmSendingLogRepository implements SmSendingLogRepositoryInterface
{
    use TableListTrait;

    public function find(int $id): ?SmSendingLog
    {
        return SmSendingLog::find($id);
    }

    public function findOrFail(int $id): SmSendingLog
    {
        return SmSendingLog::findOrFail($id);
    }

    public function create(array $data): SmSendingLog
    {
        return SmSendingLog::create($data);
    }

    public function update(int $id, array $data): SmSendingLog
    {
        $model = SmSendingLog::findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return SmSendingLog::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return SmSendingLog::whereIn('id', $ids)->delete();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SmSendingLog::query();

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
        $query = SmSendingLog::query()->select(['*']);
        $this->applyTableOperations($query, $params, ['campaign_id' => 'campaign_id', 'contact_id' => 'contact_id']);
        return $this->getResults($query, $params);
    }

    public function list(array $filters = []): array
    {
        return SmSendingLog::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(!empty($filters['limit']), fn($q) => $q->limit($filters['limit']))
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return SmSendingLog::query()
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

    public function sum(string $column): float
    {
        return (float) SmSendingLog::query()->sum($column);
    }

}
