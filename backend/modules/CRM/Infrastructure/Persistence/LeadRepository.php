<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Persistence;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\CRM\Domain\Entities\Lead;
use App\Repositories\Traits\TableListTrait;

class LeadRepository implements LeadRepositoryInterface
{
    use TableListTrait;

    public function __construct(protected Lead $model)
    {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['assignedUser', 'creator']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('company', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Lead
    {
        return $this->model->with(['assignedUser', 'creator', 'activities'])->findOrFail($id);
    }

    public function find(int $id): ?Lead
    {
        return $this->model->find($id);
    }

    public function create(array $data): Lead
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Lead
    {
        $lead = $this->findOrFail($id);
        $lead->update($data);

        return $lead;
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function bulkDelete(array $ids): int
    {
        return $this->model->destroy($ids);
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function getBySource(string $source): Collection
    {
        return $this->model->where('source', $source)->get();
    }

    public function getAssignedTo(int $userId): Collection
    {
        return $this->model->where('assigned_to', $userId)->get();
    }

    public function search(string $query): Collection
    {
        return $this->model->where('name', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->orWhere('company', 'like', '%' . $query . '%')
            ->get();
    }

    public function getCountByStatus(): array
    {
        return $this->model->groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getConversionRate(): float
    {
        $total = $this->model->count();
        if ($total === 0) {
            return 0.0;
        }

        $converted = $this->model->where('status', 'converted')->count();

        return round(($converted / $total) * 100, 2);
    }

    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'by_status' => $this->getCountByStatus(),
            'conversion_rate' => $this->getConversionRate(),
            'this_month' => $this->model->whereMonth('created_at', now()->month)->count(),
            'this_week' => $this->model->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];
    }

    public function countByMonth(int $year, int $month): int
    {
        return $this->model->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
    }
}
