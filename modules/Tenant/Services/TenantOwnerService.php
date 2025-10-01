<?php

namespace Modules\Tenant\Services;

use Modules\Tenant\Entities\TenantOwner;
use Modules\Tenant\Repository\TenantOwnerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TenantOwnerService
{
    protected TenantOwnerRepositoryInterface $tenantOwnerRepository;

    public function __construct(TenantOwnerRepositoryInterface $tenantOwnerRepository)
    {
        $this->tenantOwnerRepository = $tenantOwnerRepository;
    }

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->tenantOwnerRepository->getAll($filters, $perPage);
    }

    public function getById(int $id): ?TenantOwner
    {
        return $this->tenantOwnerRepository->getById($id);
    }

    public function getByTenantAndUser(int $tenantId, int $userId): ?TenantOwner
    {
        return $this->tenantOwnerRepository->getByTenantAndUser($tenantId, $userId);
    }

    public function create(array $data): TenantOwner
    {
        // Ensure created_by is set
        if (auth()->check() && !isset($data['created_by'])) {
            $data['created_by'] = auth()->id();
        }

        return $this->tenantOwnerRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        // Ensure updated_by is set
        if (auth()->check() && !isset($data['updated_by'])) {
            $data['updated_by'] = auth()->id();
        }

        return $this->tenantOwnerRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->tenantOwnerRepository->delete($id);
    }

    public function restore(int $id): bool
    {
        return $this->tenantOwnerRepository->restore($id);
    }

    public function getTenantOwnersForTenant(int $tenantId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->tenantOwnerRepository->getTenantOwnersForTenant($tenantId, $filters, $perPage);
    }

    public function getSuperAdminsForTenant(int $tenantId): Collection
    {
        return $this->tenantOwnerRepository->getSuperAdminsForTenant($tenantId);
    }

    public function search(string $query): Collection
    {
        return $this->tenantOwnerRepository->search($query);
    }

    public function getDashboardStats(): array
    {
        return $this->tenantOwnerRepository->getDashboardStats();
    }

    public function getByRole(string $role): Collection
    {
        return $this->tenantOwnerRepository->getByRole($role);
    }

    public function getByStatus(string $status): Collection
    {
        return $this->tenantOwnerRepository->getByStatus($status);
    }

    public function isUserTenantOwner(int $userId, int $tenantId): bool
    {
        return $this->tenantOwnerRepository->isUserTenantOwner($userId, $tenantId);
    }

    public function isUserSuperAdmin(int $userId, int $tenantId): bool
    {
        return $this->tenantOwnerRepository->isUserSuperAdmin($userId, $tenantId);
    }

    public function getTenantOwnersByUser(int $userId): Collection
    {
        return $this->tenantOwnerRepository->getTenantOwnersByUser($userId);
    }

    public function assignUserToTenant(int $userId, int $tenantId, array $additionalData = []): TenantOwner
    {
        $data = array_merge([
            'user_id' => $userId,
            'tenant_id' => $tenantId,
            'role' => 'owner',
            'is_super_admin' => false,
            'status' => 'active',
        ], $additionalData);

        return $this->create($data);
    }

    public function promoteToSuperAdmin(int $id): bool
    {
        return $this->update($id, ['is_super_admin' => true]);
    }

    public function demoteFromSuperAdmin(int $id): bool
    {
        return $this->update($id, ['is_super_admin' => false]);
    }

    public function activate(int $id): bool
    {
        return $this->update($id, ['status' => 'active']);
    }

    public function deactivate(int $id): bool
    {
        return $this->update($id, ['status' => 'inactive']);
    }

    public function suspend(int $id): bool
    {
        return $this->update($id, ['status' => 'suspended']);
    }

    public function updatePermissions(int $id, array $permissions): bool
    {
        return $this->update($id, ['permissions' => $permissions]);
    }

    public function getTenantOwnersWithPermissions(int $tenantId): Collection
    {
        return $this->tenantOwnerRepository->getTenantOwnersForTenant($tenantId, [], 100)->getCollection();
    }

    public function validateTenantOwnerAccess(int $userId, int $tenantId): bool
    {
        return $this->isUserTenantOwner($userId, $tenantId);
    }

    public function validateSuperAdminAccess(int $userId, int $tenantId): bool
    {
        return $this->isUserSuperAdmin($userId, $tenantId);
    }
}
