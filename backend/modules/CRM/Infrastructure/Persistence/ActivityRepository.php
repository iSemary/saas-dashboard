<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\Activity;

class ActivityRepository implements ActivityRepositoryInterface
{
    public function __construct(protected Activity $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['assignedUser', 'creator', 'related']);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }
        if (isset($filters['related_type'])) {
            $query->where('related_type', $filters['related_type']);
        }
        if (isset($filters['related_id'])) {
            $query->where('related_id', $filters['related_id']);
        }

        return $query->orderBy('due_date', 'asc')->paginate($perPage);
    }

    public function findOrFail(int $id): Activity
    {
        return $this->model->with(['assignedUser', 'related'])->findOrFail($id);
    }

    public function create(array $data): Activity
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Activity
    {
        $activity = $this->findOrFail($id);
        $activity->update($data);
        return $activity;
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function getByType(string $type): Collection
    {
        return $this->model->where('type', $type)->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function getAssignedTo(int $userId): Collection
    {
        return $this->model->where('assigned_to', $userId)->get();
    }

    public function getOverdue(): Collection
    {
        return $this->model->overdue()->get();
    }

    public function getUpcoming(int $days = 7): Collection
    {
        return $this->model->upcoming($days)->get();
    }

    public function getForToday(): Collection
    {
        return $this->model->forToday()->get();
    }

    public function complete(int $id, ?string $outcome = null): Activity
    {
        $activity = $this->findOrFail($id);
        $activity->complete($outcome);
        return $activity;
    }
}
