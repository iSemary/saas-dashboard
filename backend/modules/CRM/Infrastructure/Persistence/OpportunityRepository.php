<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\Opportunity;
use Modules\CRM\Domain\ValueObjects\OpportunityStage;

class OpportunityRepository implements OpportunityRepositoryInterface
{
    public function __construct(protected Opportunity $model) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['assignedUser', 'lead', 'contact', 'company']);

        if (isset($filters['stage'])) {
            $query->where('stage', $filters['stage']);
        }
        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }
        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Opportunity
    {
        return $this->model->with(['assignedUser', 'lead', 'contact', 'company', 'activities'])->findOrFail($id);
    }

    public function find(int $id): ?Opportunity
    {
        return $this->model->find($id);
    }

    public function create(array $data): Opportunity
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Opportunity
    {
        $opp = $this->findOrFail($id);
        $opp->update($data);
        return $opp;
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function bulkDelete(array $ids): int
    {
        return $this->model->destroy($ids);
    }

    public function getByStage(string $stage): Collection
    {
        return $this->model->where('stage', $stage)->get();
    }

    public function getAssignedTo(int $userId): Collection
    {
        return $this->model->where('assigned_to', $userId)->get();
    }

    public function getPipelineData(): array
    {
        $stages = OpportunityStage::cases();
        $data = [];

        foreach ($stages as $stage) {
            $opportunities = $this->model->where('stage', $stage->value)
                ->with(['assignedUser', 'contact'])
                ->get();

            $data[] = [
                'stage' => $stage->value,
                'label' => $stage->label(),
                'probability' => $stage->probability(),
                'color' => $stage->color(),
                'count' => $opportunities->count(),
                'value' => $opportunities->sum('expected_revenue'),
                'opportunities' => $opportunities,
            ];
        }

        return $data;
    }

    public function getStatistics(): array
    {
        $total = $this->model->count();
        $open = $this->model->whereNotIn('stage', ['closed_won', 'closed_lost'])->count();
        $won = $this->model->where('stage', 'closed_won')->count();
        $lost = $this->model->where('stage', 'closed_lost')->count();

        return [
            'total' => $total,
            'open' => $open,
            'won' => $won,
            'lost' => $lost,
            'win_rate' => $total > 0 ? round(($won / ($won + $lost)) * 100, 2) : 0,
            'total_value' => $this->model->sum('expected_revenue'),
            'weighted_value' => $this->model->get()->sum(fn ($o) => $o->weightedRevenue()),
        ];
    }
}
