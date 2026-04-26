<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\OvertimeRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentOvertimeRequestRepository implements OvertimeRequestRepositoryInterface
{
    public function find(string $id): ?OvertimeRequest
    {
        return OvertimeRequest::find($id);
    }

    public function findOrFail(string $id): OvertimeRequest
    {
        return OvertimeRequest::findOrFail($id);
    }

    public function create(array $data): OvertimeRequest
    {
        return OvertimeRequest::create($data);
    }

    public function update(string $id, array $data): OvertimeRequest
    {
        $item = $this->findOrFail($id);
        $item->update($data);
        return $item->fresh();
    }

    public function delete(string $id): bool
    {
        $item = $this->find($id);
        return $item ? $item->delete() : false;
    }

    public function paginateByUser(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        return OvertimeRequest::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getOvertimeSummary(string $from, string $to): Collection
    {
        return OvertimeRequest::whereBetween('date', [$from, $to])
            ->selectRaw('status, count(*) as count, sum(requested_minutes) as total_minutes')
            ->groupBy('status')
            ->get();
    }
}
