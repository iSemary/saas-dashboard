<?php

namespace Modules\Customer\Services;

use Modules\Customer\DTOs\CreateBrandData;
use Modules\Customer\DTOs\UpdateBrandData;
use Modules\Customer\Entities\Brand;
use Modules\Customer\Repository\BrandRepositoryInterface;
use Modules\Tenant\Entities\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BrandService
{
    protected BrandRepositoryInterface $brandRepository;
    protected BrandModuleSubscriptionService $moduleService;

    public function __construct(
        BrandRepositoryInterface $brandRepository,
        BrandModuleSubscriptionService $moduleService,
    ) {
        $this->brandRepository = $brandRepository;
        $this->moduleService = $moduleService;
    }

    public function getAll(array $params = []): LengthAwarePaginator|Collection
    {
        return $this->brandRepository->getAll($params);
    }

    public function getDataTables()
    {
        return $this->brandRepository->datatables();
    }

    public function getTenantDataTables(int $tenantId)
    {
        return $this->brandRepository->tenantDataTables($tenantId);
    }

    public function getById(int $id): ?Brand
    {
        return $this->brandRepository->getById($id);
    }

    public function findOrFail(int $id): Brand
    {
        $brand = $this->brandRepository->getById($id);
        if (!$brand) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                'No query results for model [' . Brand::class . '] ' . $id
            );
        }
        return $brand;
    }

    public function getBySlug(string $slug): ?Brand
    {
        return $this->brandRepository->getBySlug($slug);
    }

    public function create(CreateBrandData $data): Brand
    {
        $arrayData = [
            'name' => $data->name,
            'slug' => $data->slug,
            'domain' => $data->domain,
            'is_active' => $data->is_active ?? true,
        ];

        return $this->brandRepository->create($arrayData);
    }

    public function update(int $id, UpdateBrandData $data): bool
    {
        $brand = $this->findOrFail($id);
        $arrayData = $data->toArray();

        return $this->brandRepository->update($id, $arrayData);
    }

    public function delete(int $id): bool
    {
        $brand = $this->findOrFail($id);

        // Delete logo file if exists
        if ($brand->logo) {
            $this->deleteLogo($brand->logo);
        }

        return $this->brandRepository->delete($id);
    }

    public function restore(int $id): bool
    {
        return $this->brandRepository->restore($id);
    }

    public function getByTenant(int $tenantId): Collection
    {
        return $this->brandRepository->getByTenant($tenantId);
    }

    public function search(string $query): Collection
    {
        return $this->brandRepository->search($query);
    }

    public function getDashboardStats(): array
    {
        return $this->brandRepository->getDashboardStats();
    }

    /**
     * Upload brand logo
     */
    private function uploadLogo(UploadedFile $file): string
    {
        $path = $file->store('brands/logos', 'public');
        return $path;
    }

    /**
     * Delete brand logo
     */
    private function deleteLogo(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Get brands for a specific tenant with pagination
     */
    public function getBrandsForTenant(int $tenantId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $filters['tenant_id'] = $tenantId;
        return $this->getAll($filters, $perPage);
    }

    /**
     * Validate brand slug uniqueness within tenant
     */
    public function isSlugUnique(string $slug, int $tenantId, ?int $excludeId = null): bool
    {
        $query = Brand::where('slug', $slug)
            ->where('tenant_id', $tenantId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->count() === 0;
    }

    /**
     * Generate unique slug for brand
     */
    public function generateUniqueSlug(string $name, int $tenantId, ?int $excludeId = null): string
    {
        $baseSlug = \Illuminate\Support\Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (!$this->isSlugUnique($slug, $tenantId, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Sync brand modules - subscribe to new ones, unsubscribe from removed ones.
     */
    public function syncBrandModules(int $brandId, array $moduleKeys): void
    {
        // Get current active subscriptions
        $currentSubscriptions = $this->moduleService->getActiveSubscriptions($brandId);
        $currentKeys = $currentSubscriptions->pluck('module_key')->toArray();

        // Determine which modules to add and which to remove
        $toAdd = array_diff($moduleKeys, $currentKeys);
        $toRemove = array_diff($currentKeys, $moduleKeys);

        // Subscribe to new modules
        if (!empty($toAdd)) {
            $this->moduleService->subscribeToModules($brandId, $toAdd);
        }

        // Unsubscribe from removed modules
        if (!empty($toRemove)) {
            $this->moduleService->unsubscribeFromModules($brandId, $toRemove);
        }
    }
}
