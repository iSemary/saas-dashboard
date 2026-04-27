<?php

namespace Modules\Customer\Repository\Tenant;

use Modules\Customer\Entities\Brand;
use Modules\Customer\Entities\Tenant\Brand as TenantBrand;
use Modules\Customer\Repository\BrandRepositoryInterface;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BrandRepository implements BrandRepositoryInterface
{
    use TableListTrait;

    public function getAll(array $params = []): LengthAwarePaginator|Collection
    {
        return $this->tableList(
            TenantBrand::class,
            $params,
            ['name' => 'name', 'slug' => 'slug', 'domain' => 'domain'],  // searchable columns
            ['id' => 'id', 'name' => 'name', 'slug' => 'slug', 'domain' => 'domain']  // sortable columns
        );
    }

    public function getAllLegacy(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TenantBrand::with(['creator', 'updater']);

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
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

    public function getById(int $id): ?Brand
    {
        return TenantBrand::with(['creator', 'updater'])->find($id);
    }

    public function getBySlug(string $slug): ?Brand
    {
        return TenantBrand::with(['creator', 'updater'])->where('slug', $slug)->first();
    }

    public function create(array $data): Brand
    {
        return TenantBrand::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $brand = $this->getById($id);
        if ($brand) {
            return $brand->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $brand = $this->getById($id);
        if ($brand) {
            return $brand->delete();
        }
        return false;
    }

    public function restore(int $id): bool
    {
        $brand = Brand::withTrashed()->find($id);
        if ($brand) {
            return $brand->restore();
        }
        return false;
    }

    public function search(string $query): Collection
    {
        return TenantBrand::with(['creator', 'updater'])
            ->search($query)
            ->orderBy('name')
            ->get();
    }

    public function getDashboardStats(): array
    {
        return [
            'total' => TenantBrand::count(),
            'active' => TenantBrand::where('status', 'active')->count(),
            'inactive' => TenantBrand::where('status', 'inactive')->count(),
            'suspended' => TenantBrand::where('status', 'suspended')->count(),
            'deleted' => TenantBrand::onlyTrashed()->count(),
            'recent_30_days' => TenantBrand::where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }

    public function getByTenant(int $tenantId): Collection
    {
        return TenantBrand::where('tenant_id', $tenantId)
            ->with(['creator', 'updater'])
            ->orderBy('name')
            ->get();
    }
}
