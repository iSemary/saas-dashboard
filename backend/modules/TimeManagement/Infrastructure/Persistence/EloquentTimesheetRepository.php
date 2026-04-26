<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\Timesheet;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentTimesheetRepository implements TimesheetRepositoryInterface
{
    use TableListTrait;

    public function find(string $id): ?Timesheet
    {
        return Timesheet::find($id);
    }

    public function findOrFail(string $id): Timesheet
    {
        return Timesheet::findOrFail($id);
    }

    public function create(array $data): Timesheet
    {
        return Timesheet::create($data);
    }

    public function update(string $id, array $data): Timesheet
    {
        $ts = $this->findOrFail($id);
        $ts->update($data);
        return $ts->fresh();
    }

    public function delete(string $id): bool
    {
        $ts = $this->find($id);
        return $ts ? $ts->delete() : false;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Timesheet::query();

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('period_start', 'desc')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return Timesheet::query()
            ->when(!empty($filters['user_id']), fn($q) => $q->where('user_id', $filters['user_id']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->orderBy('period_start', 'desc')
            ->get()
            ->all();
    }

    public function count(array $filters = []): int
    {
        return Timesheet::query()
            ->when(!empty($filters['tenant_id']), fn($q) => $q->where('tenant_id', $filters['tenant_id']))
            ->when(!empty($filters['user_id']), fn($q) => $q->where('user_id', $filters['user_id']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->count();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = Timesheet::query();
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }

    public function getSubmittedHoursSummary(string $from, string $to): \Illuminate\Support\Collection
    {
        return Timesheet::whereBetween('period_start', [$from, $to])
            ->selectRaw('status, count(*) as count, sum(total_minutes) as total_minutes')
            ->groupBy('status')
            ->get();
    }
}
