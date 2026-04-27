<?php

namespace Modules\Customer\Repository;

use Modules\Customer\Entities\Brand;
use Modules\Customer\Repository\BrandRepositoryInterface;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BrandRepository implements BrandRepositoryInterface
{
    use TableListTrait;

    public function getAll(array $params = []): LengthAwarePaginator|Collection
    {
        return $this->tableList(
            Brand::class,
            $params,
            ['name' => 'name', 'slug' => 'slug', 'domain' => 'domain'],  // searchable columns
            ['id' => 'id', 'name' => 'name', 'slug' => 'slug', 'domain' => 'domain']  // sortable columns
        );
    }

    public function getAllLegacy(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Brand::on('landlord')->with(['tenant', 'creator', 'updater']);

        if (isset($filters['tenant_id'])) {
            $query->forTenant($filters['tenant_id']);
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

    public function getById(int $id): ?Brand
    {
        return Brand::with(['tenant', 'creator', 'updater'])->find($id);
    }

    public function getBySlug(string $slug): ?Brand
    {
        return Brand::with(['tenant', 'creator', 'updater'])->where('slug', $slug)->first();
    }

    public function create(array $data): Brand
    {
        return Brand::create($data);
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
        $brand = Brand::on('landlord')->withTrashed()->find($id);
        if ($brand) {
            return $brand->restore();
        }
        return false;
    }

    public function getByTenant(int $tenantId): Collection
    {
        return Brand::on('landlord')
            ->with(['creator', 'updater'])
            ->forTenant($tenantId)
            ->orderBy('name')
            ->get();
    }

    public function search(string $query): Collection
    {
        return Brand::on('landlord')->with(['tenant', 'creator', 'updater'])
            ->search($query)
            ->orderBy('name')
            ->get();
    }

    public function getDashboardStats(): array
    {
        return [
            'total' => Brand::on('landlord')->count(),
            'active' => Brand::on('landlord')->count(),
            'deleted' => Brand::on('landlord')->onlyTrashed()->count(),
            'recent_30_days' => Brand::on('landlord')->where('created_at', '>=', now()->subDays(30))->count(),
            'by_tenant' => Brand::on('landlord')->selectRaw('tenant_id, COUNT(*) as count')
                ->groupBy('tenant_id')
                ->with('tenant:id,name')
                ->get()
                ->pluck('count', 'tenant.name')
                ->toArray(),
        ];
    }
}
