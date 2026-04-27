<?php

namespace Modules\Customer\Repository;

use Modules\Customer\Entities\Branch;
use Modules\Customer\Repository\BranchRepositoryInterface;
use App\Repositories\Traits\TableListTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BranchRepository implements BranchRepositoryInterface
{
    use TableListTrait;

    public function getAll(array $params = []): LengthAwarePaginator|Collection
    {
        return $this->tableList(
            Branch::class,
            $params,
            ['name' => 'name', 'slug' => 'slug'],  // searchable columns
            ['id' => 'id', 'name' => 'name', 'slug' => 'slug', 'brand_id' => 'brand_id']  // sortable columns
        );
    }

    public function getAllLegacy(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Branch::with(['creator', 'updater']);

        if (isset($filters['brand_id'])) {
            $query->forBrand($filters['brand_id']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['city'])) {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        if (isset($filters['state'])) {
            $query->where('state', 'like', '%' . $filters['state'] . '%');
        }

        if (isset($filters['country'])) {
            $query->where('country', 'like', '%' . $filters['country'] . '%');
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

    public function getById(int $id): ?Branch
    {
        return Branch::with(['creator', 'updater'])->find($id);
    }

    public function getByCode(string $code): ?Branch
    {
        return Branch::with(['creator', 'updater'])->where('code', $code)->first();
    }

    public function create(array $data): Branch
    {
        return Branch::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $branch = $this->getById($id);
        if ($branch) {
            return $branch->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $branch = $this->getById($id);
        if ($branch) {
            return $branch->delete();
        }
        return false;
    }

    public function restore(int $id): bool
    {
        $branch = Branch::withTrashed()->find($id);
        if ($branch) {
            return $branch->restore();
        }
        return false;
    }

    public function getByBrand(int $brandId): Collection
    {
        return Branch::with(['creator', 'updater'])
            ->forBrand($brandId)
            ->orderBy('name')
            ->get();
    }

    public function search(string $query): Collection
    {
        return Branch::with(['creator', 'updater'])
            ->search($query)
            ->orderBy('name')
            ->get();
    }

    public function getDashboardStats(): array
    {
        return [
            'total' => Branch::count(),
            'active' => Branch::where('status', 'active')->count(),
            'inactive' => Branch::where('status', 'inactive')->count(),
            'suspended' => Branch::where('status', 'suspended')->count(),
            'deleted' => Branch::onlyTrashed()->count(),
            'recent_30_days' => Branch::where('created_at', '>=', now()->subDays(30))->count(),
            'by_brand' => Branch::selectRaw('brand_id, COUNT(*) as count')
                ->groupBy('brand_id')
                ->get()
                ->mapWithKeys(function ($item) {
                    try {
                        $brand = Brand::on('landlord')->find($item->brand_id);
                        return [$brand ? $brand->name : 'Unknown Brand' => $item->count];
                    } catch (\Exception $e) {
                        return ['Unknown Brand' => $item->count];
                    }
                })
                ->toArray(),
            'by_location' => Branch::selectRaw('country, COUNT(*) as count')
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->pluck('count', 'country')
                ->toArray(),
        ];
    }

    public function getActiveBranches(): Collection
    {
        return Branch::active()
            ->orderBy('name')
            ->get();
    }

    public function getBranchesByLocation(string $city = null, string $state = null, string $country = null): Collection
    {
        $query = Branch::query();

        if ($city) {
            $query->where('city', 'like', '%' . $city . '%');
        }

        if ($state) {
            $query->where('state', 'like', '%' . $state . '%');
        }

        if ($country) {
            $query->where('country', 'like', '%' . $country . '%');
        }

        return $query->orderBy('name')->get();
    }
}
