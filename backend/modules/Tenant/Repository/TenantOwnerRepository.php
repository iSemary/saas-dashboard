<?php

namespace Modules\Tenant\Repository;

use Modules\Tenant\Entities\TenantOwner;
use Modules\Tenant\Repository\TenantOwnerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TenantOwnerRepository implements TenantOwnerRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TenantOwner::with(['tenant', 'user', 'creator', 'updater']);

        if (isset($filters['tenant_id'])) {
            $query->forTenant($filters['tenant_id']);
        }

        if (isset($filters['role'])) {
            $query->byRole($filters['role']);
        }

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['is_super_admin'])) {
            $query->where('is_super_admin', $filters['is_super_admin']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getById(int $id): ?TenantOwner
    {
        return TenantOwner::with(['tenant', 'user', 'creator', 'updater'])->find($id);
    }

    public function getByTenantAndUser(int $tenantId, int $userId): ?TenantOwner
    {
        return TenantOwner::where('tenant_id', $tenantId)
                          ->where('user_id', $userId)
                          ->first();
    }

    public function create(array $data): TenantOwner
    {
        return TenantOwner::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $tenantOwner = $this->getById($id);
        if ($tenantOwner) {
            return $tenantOwner->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $tenantOwner = $this->getById($id);
        if ($tenantOwner) {
            return $tenantOwner->delete();
        }
        return false;
    }

    public function restore(int $id): bool
    {
        $tenantOwner = TenantOwner::onlyTrashed()->find($id);
        if ($tenantOwner) {
            return $tenantOwner->restore();
        }
        return false;
    }

    public function getTenantOwnersForTenant(int $tenantId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters['tenant_id'] = $tenantId;
        return $this->getAll($filters, $perPage);
    }

    public function getSuperAdminsForTenant(int $tenantId): Collection
    {
        return TenantOwner::with(['user'])
                          ->where('tenant_id', $tenantId)
                          ->where('is_super_admin', true)
                          ->where('status', 'active')
                          ->get();
    }

    public function search(string $query): Collection
    {
        return TenantOwner::with(['tenant', 'user'])
                          ->search($query)
                          ->get();
    }

    public function getDashboardStats(): array
    {
        return [
            'total' => TenantOwner::count(),
            'active' => TenantOwner::where('status', 'active')->count(),
            'inactive' => TenantOwner::where('status', 'inactive')->count(),
            'suspended' => TenantOwner::where('status', 'suspended')->count(),
            'super_admins' => TenantOwner::where('is_super_admin', true)->count(),
            'recent_30_days' => TenantOwner::where('created_at', '>=', now()->subDays(30))->count(),
            'by_tenant' => TenantOwner::select('tenant_id', \DB::raw('count(*) as total'))
                                    ->groupBy('tenant_id')
                                    ->with('tenant')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [$item->tenant->name ?? 'N/A' => $item->total];
                                    })
                                    ->toArray(),
            'by_role' => TenantOwner::select('role', \DB::raw('count(*) as total'))
                                  ->groupBy('role')
                                  ->get()
                                  ->mapWithKeys(function ($item) {
                                      return [$item->role => $item->total];
                                  })
                                  ->toArray(),
        ];
    }

    public function getByRole(string $role): Collection
    {
        return TenantOwner::with(['tenant', 'user'])
                          ->byRole($role)
                          ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return TenantOwner::with(['tenant', 'user'])
                          ->byStatus($status)
                          ->get();
    }

    public function isUserTenantOwner(int $userId, int $tenantId): bool
    {
        return TenantOwner::where('user_id', $userId)
                          ->where('tenant_id', $tenantId)
                          ->where('status', 'active')
                          ->exists();
    }

    public function isUserSuperAdmin(int $userId, int $tenantId): bool
    {
        return TenantOwner::where('user_id', $userId)
                          ->where('tenant_id', $tenantId)
                          ->where('is_super_admin', true)
                          ->where('status', 'active')
                          ->exists();
    }

    public function getTenantOwnersByUser(int $userId): Collection
    {
        return TenantOwner::with(['tenant'])
                          ->where('user_id', $userId)
                          ->where('status', 'active')
                          ->get();
    }
}
