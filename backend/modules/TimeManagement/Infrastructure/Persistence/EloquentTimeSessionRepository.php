<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\TimeSession;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentTimeSessionRepository implements TimeSessionRepositoryInterface
{
    public function find(string $id): ?TimeSession
    {
        return TimeSession::find($id);
    }

    public function findOrFail(string $id): TimeSession
    {
        return TimeSession::findOrFail($id);
    }

    public function create(array $data): TimeSession
    {
        return TimeSession::create($data);
    }

    public function delete(string $id): bool
    {
        $item = $this->find($id);
        return $item ? $item->delete() : false;
    }

    public function paginateByUser(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        return TimeSession::where('user_id', $userId)
            ->orderBy('started_at', 'desc')
            ->paginate($perPage);
    }

    public function findActiveByUser(string $userId): ?TimeSession
    {
        return TimeSession::where('user_id', $userId)
            ->where('is_running', true)
            ->first();
    }
}
