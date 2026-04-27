<?php

namespace Modules\Customer\Services\Tenant;

use Modules\Customer\Repositories\Tenant\Contracts\BrandInterface;
use Modules\Customer\Entities\Tenant\Brand;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class BrandService
{
    protected $repository;

    public function __construct(BrandInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all brands
     */
    public function getAll(array $conditions = [])
    {
        return $this->repository->getAll($conditions);
    }

    /**
     * Get a brand by ID
     */
    public function get(int $id)
    {
        return $this->repository->get($id);
    }

    /**
     * Create a new brand
     */
    public function create(array $data)
    {
        // Handle logo upload if present
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile)
        {
            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        return $this->repository->create($data);
    }

    /**
     * Update a brand
     */
    public function update(int $id, array $data)
    {
        // Handle logo upload if present
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile)
        {
            // Delete old logo if exists
            $brand = $this->get($id);
            if ($brand->logo)
            {
                $this->deleteLogo($brand->logo);
            }

            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Delete a brand
     */
    public function delete(int $id)
    {
        $brand = $this->get($id);

        // Delete logo file if exists
        if ($brand->logo)
        {
            $this->deleteLogo($brand->logo);
        }

        return $this->repository->delete($id);
    }

    /**
     * Restore a brand
     */
    public function restore(int $id)
    {
        return $this->repository->restore($id);
    }

    /**
     * Get brands with their modules
     */
    public function getBrandsWithModules()
    {
        return $this->repository->getBrandsWithModules();
    }

    /**
     * Get modules for a specific brand
     */
    public function getBrandModules(int $brandId)
    {
        return $this->repository->getBrandModules($brandId);
    }

    /**
     * Assign modules to a brand
     */
    public function assignModules(int $brandId, array $moduleIds)
    {
        return $this->repository->assignModules($brandId, $moduleIds);
    }

    /**
     * Search brands
     */
    public function search(string $search)
    {
        return $this->repository->search($search);
    }

    /**
     * Upload brand logo
     */
    private function uploadLogo(UploadedFile $file)
    {
        $path = $file->store('brands', 'public');
        return $path;
    }

    /**
     * Delete brand logo
     */
    private function deleteLogo(string $logoPath)
    {
        if (Storage::disk('public')->exists($logoPath))
        {
            Storage::disk('public')->delete($logoPath);
        }
    }

    /**
     * Get brands for dashboard display
     */
    public function getBrandsForDashboard()
    {
        return $this->repository->getAll()
            ->map(function ($brand)
            {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'logo_url' => $brand->logo_url,
                    'modules_count' => $brand->modules_count,
                    'status' => $brand->status,
                ];
            });
    }

    /**
     * Validate brand data
     */
    public function validateBrandData(array $data)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        return validator($data, $rules);
    }
}
