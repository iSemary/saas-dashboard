<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\TimeEntry;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentTimeEntryRepository implements TimeEntryRepositoryInterface
{
    use TableListTrait;

    public function find(string $id): ?TimeEntry
    {
        return TimeEntry::find($id);
    }

    public function findOrFail(string $id): TimeEntry
    {
        return TimeEntry::findOrFail($id);
    }

    public function create(array $data): TimeEntry
    {
        return TimeEntry::create($data);
    }

    public function update(string $id, array $data): TimeEntry
    {
        $entry = $this->findOrFail($id);
        $entry->update($data);
        return $entry->fresh();
    }

    public function delete(string $id): bool
    {
        $entry = $this->find($id);
        return $entry ? $entry->delete() : false;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TimeEntry::query();

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        return $query->orderBy('date', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return TimeEntry::query()
            ->when(!empty($filters['user_id']), fn($q) => $q->where('user_id', $filters['user_id']))
            ->when(!empty($filters['project_id']), fn($q) => $q->where('project_id', $filters['project_id']))
            ->orderBy('date', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return TimeEntry::query()
            ->when(!empty($filters['tenant_id']), fn($q) => $q->where('tenant_id', $filters['tenant_id']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = TimeEntry::query();
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }

    public function sumDurationByUserAndDate(string $userId, string $date): int
    {
        return (int) TimeEntry::where('user_id', $userId)
            ->where('date', $date)
            ->sum('duration_minutes');
    }

    public function getUtilization(string $userId, string $from, string $to): array
    {
        $totalMinutes = (int) TimeEntry::where('user_id', $userId)
            ->whereBetween('date', [$from, $to])
            ->sum('duration_minutes');

        $billableMinutes = (int) TimeEntry::where('user_id', $userId)
            ->whereBetween('date', [$from, $to])
            ->where('is_billable', true)
            ->sum('duration_minutes');

        return [
            'total_minutes' => $totalMinutes,
            'billable_minutes' => $billableMinutes,
            'non_billable_minutes' => $totalMinutes - $billableMinutes,
        ];
    }

    public function getAnomalies(int $limit = 50): array
    {
        return TimeEntry::where('duration_minutes', '>', 600)
            ->orWhereNull('project_id')
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getBillableRatio(string $userId, string $from, string $to): array
    {
        $billable = (int) TimeEntry::where('user_id', $userId)->where('is_billable', true)
            ->whereBetween('date', [$from, $to])->sum('duration_minutes');
        $nonBillable = (int) TimeEntry::where('user_id', $userId)->where('is_billable', false)
            ->whereBetween('date', [$from, $to])->sum('duration_minutes');

        return [
            'billable' => $billable,
            'non_billable' => $nonBillable,
        ];
    }
}
