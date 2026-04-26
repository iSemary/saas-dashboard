<?php

namespace Modules\CRM\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\CRM\Models\Company;
use OwenIt\Auditing\Models\Audit;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Company::with(['assignedUser', 'creator', 'contacts']);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        } else {
            $query->where('type', 'customer');
        }

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (isset($filters['industry']) && $filters['industry']) {
            $query->where('industry', $filters['industry']);
        }

        if (isset($filters['assigned_to']) && $filters['assigned_to']) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['brand_id']) && $filters['brand_id']) {
            $query->where('brand_id', $filters['brand_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findOrFail(int $id): Company
    {
        return Company::with(['assignedUser', 'creator', 'contacts', 'opportunities'])->findOrFail($id);
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function update(int $id, array $data): Company
    {
        $company = Company::findOrFail($id);
        $company->update($data);
        return $company->load(['assignedUser', 'creator', 'contacts']);
    }

    public function delete(int $id): bool
    {
        return Company::findOrFail($id)->delete();
    }

    public function bulkDelete(array $ids): int
    {
        return Company::whereIn('id', $ids)->delete();
    }

    public function getActivity(int $id, int $perPage = 20): LengthAwarePaginator
    {
        return Audit::where('auditable_type', Company::class)
            ->where('auditable_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
