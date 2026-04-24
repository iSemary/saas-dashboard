<?php

namespace App\Repositories\CrossDb;

use Illuminate\Support\Facades\DB;
use Modules\Customer\Entities\Tenant\Brand;
use Modules\Utilities\Entities\Module;

class TenantRepository implements TenantRepositoryInterface
{
    public function getBrands(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Brand::query();

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        $brands = $query->select(['id', 'name', 'slug', 'description', 'logo', 'status', 'created_at'])
                        ->orderBy('name')
                        ->get();

        $brands->each(function ($brand) {
            $brand->modules_count = DB::table('brand_module')
                ->where('brand_id', $brand->id)
                ->count();
        });

        return $brands;
    }

    public function findBrand(int $id): ?Brand
    {
        $brand = Brand::select(['id', 'name', 'slug', 'description', 'logo', 'status', 'created_at'])
                     ->find($id);

        if ($brand) {
            $brand->modules_count = DB::table('brand_module')
                ->where('brand_id', $brand->id)
                ->count();
        }

        return $brand;
    }

    public function getBrandModules(int $brandId): \Illuminate\Database\Eloquent\Collection
    {
        $moduleIds = DB::table('brand_module')
            ->where('brand_id', $brandId)
            ->pluck('module_id')
            ->toArray();

        if (empty($moduleIds)) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        return Module::whereIn('id', $moduleIds)
                     ->select(['id', 'module_key', 'name', 'description', 'icon', 'status'])
                     ->get();
    }

    public function assignBrandModules(int $brandId, array $moduleIds): int
    {
        Brand::findOrFail($brandId);

        DB::table('brand_module')->where('brand_id', $brandId)->delete();

        $assignments = [];
        foreach ($moduleIds as $moduleId) {
            $assignments[] = [
                'brand_id' => $brandId,
                'module_id' => $moduleId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('brand_module')->insert($assignments);

        return count($assignments);
    }

    public function getBrandStats(): array
    {
        return [
            'total_brands' => Brand::count(),
            'active_brands' => Brand::where('status', 'active')->count(),
            'inactive_brands' => Brand::where('status', 'inactive')->count(),
            'brands_with_modules' => DB::table('brand_module')
                ->distinct('brand_id')
                ->count(),
        ];
    }
}
