<?php

namespace Modules\CRM\Repositories;

use Modules\CRM\Models\Lead;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class LeadRepository
{
    protected $model;

    public function __construct(Lead $model)
    {
        $this->model = $model;
    }

    /**
     * Get all leads with pagination and filters.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['assignedUser', 'creator']);

        // Apply filters
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

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find lead by ID.
     */
    public function find(int $id): ?Lead
    {
        return $this->model->with(['assignedUser', 'creator', 'activities'])->find($id);
    }

    /**
     * Create a new lead.
     */
    public function create(array $data): Lead
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing lead.
     */
    public function update(int $id, array $data): bool
    {
        $lead = $this->find($id);
        
        if (!$lead) {
            return false;
        }

        return $lead->update($data);
    }

    /**
     * Delete a lead.
     */
    public function delete(int $id): bool
    {
        $lead = $this->find($id);
        
        if (!$lead) {
            return false;
        }

        return $lead->delete();
    }

    /**
     * Get leads by status.
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->byStatus($status)->get();
    }

    /**
     * Get leads by source.
     */
    public function getBySource(string $source): Collection
    {
        return $this->model->bySource($source)->get();
    }

    /**
     * Get leads assigned to user.
     */
    public function getAssignedTo(int $userId): Collection
    {
        return $this->model->assignedTo($userId)->get();
    }

    /**
     * Search leads.
     */
    public function search(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('name', 'like', '%' . $query . '%')
              ->orWhere('email', 'like', '%' . $query . '%')
              ->orWhere('company', 'like', '%' . $query . '%')
              ->orWhere('phone', 'like', '%' . $query . '%');
        })->get();
    }

    /**
     * Get total count.
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Get count by status.
     */
    public function getCountByStatus(): array
    {
        return $this->model->selectRaw('status, COUNT(*) as count')
                          ->groupBy('status')
                          ->pluck('count', 'status')
                          ->toArray();
    }

    /**
     * Get count by source.
     */
    public function getCountBySource(): array
    {
        return $this->model->selectRaw('source, COUNT(*) as count')
                          ->groupBy('source')
                          ->pluck('count', 'source')
                          ->toArray();
    }

    /**
     * Get conversion rate.
     */
    public function getConversionRate(): float
    {
        $total = $this->model->count();
        $converted = $this->model->where('status', 'converted')->count();
        
        return $total > 0 ? ($converted / $total) * 100 : 0;
    }

    /**
     * Get this month's count.
     */
    public function getThisMonthCount(): int
    {
        return $this->model->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                          ->count();
    }

    /**
     * Get recent leads.
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->with(['assignedUser'])
                          ->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Get leads by date range.
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])->get();
    }
}
