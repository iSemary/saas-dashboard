<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Infrastructure\Persistence;

use Modules\TimeManagement\Domain\Entities\CalendarEvent;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentCalendarEventRepository implements CalendarEventRepositoryInterface
{
    use TableListTrait;

    public function find(string $id): ?CalendarEvent
    {
        return CalendarEvent::find($id);
    }

    public function findOrFail(string $id): CalendarEvent
    {
        return CalendarEvent::findOrFail($id);
    }

    public function create(array $data): CalendarEvent
    {
        return CalendarEvent::create($data);
    }

    public function update(string $id, array $data): CalendarEvent
    {
        $event = $this->findOrFail($id);
        $event->update($data);
        return $event->fresh();
    }

    public function delete(string $id): bool
    {
        $event = $this->find($id);
        return $event ? $event->delete() : false;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = CalendarEvent::query();

        if (!empty($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['starts_after'])) {
            $query->where('starts_at', '>=', $filters['starts_after']);
        }
        if (!empty($filters['ends_before'])) {
            $query->where('ends_at', '<=', $filters['ends_before']);
        }

        return $query->orderBy('starts_at')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        $query = CalendarEvent::query()
            ->when(!empty($filters['user_id']), fn($q) => $q->where('user_id', $filters['user_id']))
            ->when(!empty($filters['starts_after']), fn($q) => $q->where('starts_at', '>=', $filters['starts_after']))
            ->when(!empty($filters['upcoming']), fn($q) => $q->where('starts_at', '>=', now()));

        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        return $query->orderBy('starts_at')->get()->all();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = CalendarEvent::query();
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
