<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Infrastructure\Persistence;

use Modules\ProjectManagement\Domain\Entities\Milestone;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentMilestoneRepository implements MilestoneRepositoryInterface
{
    use TableListTrait;

    public function find(string $id): ?Milestone
    {
        return Milestone::find($id);
    }

    public function findOrFail(string $id): Milestone
    {
        return Milestone::findOrFail($id);
    }

    public function create(array $data): Milestone
    {
        return Milestone::create($data);
    }

    public function update(string $id, array $data): Milestone
    {
        $milestone = $this->findOrFail($id);
        $milestone->update($data);
        return $milestone->fresh();
    }

    public function delete(string $id): bool
    {
        $milestone = $this->find($id);
        return $milestone ? $milestone->delete() : false;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Milestone::query();

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('due_date')->paginate($perPage);
    }

    public function list(array $filters = []): array
    {
        return Milestone::query()
            ->when(!empty($filters['project_id']), fn($q) => $q->where('project_id', $filters['project_id']))
            ->orderBy('due_date')
            ->get()
            ->all();
    }

    public function getTableList(array $params): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = Milestone::query();
        $this->applyTableOperations($query, $params);
        return $this->getResults($query, $params);
    }
}
