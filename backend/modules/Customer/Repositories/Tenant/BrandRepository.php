<?php

namespace Modules\Customer\Repositories\Tenant;

use Modules\Customer\Entities\Tenant\Brand;
use Modules\Customer\Repositories\Tenant\Contracts\BrandInterface;
class BrandRepository implements BrandInterface
{
    protected $model;

    public function __construct(Brand $model)
    {
        $this->model = $model;
    }

    /**
     * Get all brands
     */
    public function getAll(array $conditions = [])
    {
        $query = $this->model->query();

        if (!empty($conditions))
        {
            foreach ($conditions as $condition)
            {
                $query->where($condition['column'], $condition['operator'], $condition['value']);
            }
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get a brand by ID
     */
    public function get(int $id)
    {
        return $this->model->with(['creator', 'updater', 'modules'])->findOrFail($id);
    }

    /**
     * Create a new brand
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a brand
     */
    public function update(int $id, array $data)
    {
        $brand = $this->get($id);
        $brand->update($data);
        return $brand;
    }

    /**
     * Delete a brand
     */
    public function delete(int $id)
    {
        $brand = $this->get($id);
        return $brand->delete();
    }

    /**
     * Restore a brand
     */
    public function restore(int $id)
    {
        $brand = $this->model->withTrashed()->findOrFail($id);
        return $brand->restore();
    }

    /**
     * Get brands with their modules
     */
    public function getBrandsWithModules()
    {
        return $this->model->with('modules')->get();
    }

    /**
     * Get modules for a specific brand
     */
    public function getBrandModules(int $brandId)
    {
        try {
            // Get module IDs from pivot table
            $moduleIds = \DB::table('brand_module')
                ->where('brand_id', $brandId)
                ->pluck('module_id')
                ->toArray();

            if (empty($moduleIds)) {
                return collect();
            }

            // Get modules from landlord database directly
            $modules = \DB::connection('landlord')
                ->table('modules')
                ->whereIn('id', $moduleIds)
                ->select(['id', 'module_key', 'name', 'description', 'icon', 'status'])
                ->get();

            return $modules;

        } catch (\Exception $e) {
            \Log::error('BrandRepository getBrandModules Error: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Assign modules to a brand
     */
    public function assignModules(int $brandId, array $moduleIds)
    {
        // Clear existing assignments
        \DB::table('brand_module')->where('brand_id', $brandId)->delete();

        // Insert new assignments
        $assignments = [];
        foreach ($moduleIds as $moduleId) {
            $assignments[] = [
                'brand_id' => $brandId,
                'module_id' => $moduleId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return \DB::table('brand_module')->insert($assignments);
    }

    /**
     * Search brands
     */
    public function search(string $search)
    {
        return $this->model->search($search)->get();
    }

    /**
     * Get brands for dashboard display
     */
    public function getBrandsForDashboard()
    {
        return $this->model->select(['id', 'name', 'logo', 'status'])
                          ->withCount('modules')
                          ->orderBy('name')
                          ->limit(8)
                          ->get()
                          ->map(function ($brand) {
                              return [
                                  'id' => $brand->id,
                                  'name' => $brand->name,
                                  'logo_url' => $brand->logo_url,
                                  'status' => $brand->status,
                                  'modules_count' => $brand->modules_count ?? 0,
                              ];
                          });
    }
}
