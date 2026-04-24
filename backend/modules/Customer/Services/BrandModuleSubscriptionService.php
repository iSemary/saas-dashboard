<?php

namespace Modules\Customer\Services;

use Modules\Customer\Entities\BrandModuleSubscription;
use Modules\Customer\Repository\BrandModuleSubscriptionRepositoryInterface;
use App\Services\CrossDb\LandlordService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BrandModuleSubscriptionService
{
    protected BrandModuleSubscriptionRepositoryInterface $subscriptionRepository;
    protected LandlordService $landlordService;

    public function __construct(
        BrandModuleSubscriptionRepositoryInterface $subscriptionRepository,
        LandlordService $landlordService,
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->landlordService = $landlordService;
    }

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->subscriptionRepository->getAll($filters, $perPage);
    }

    public function getDataTables()
    {
        return $this->subscriptionRepository->datatables();
    }

    public function getById(int $id): ?BrandModuleSubscription
    {
        return $this->subscriptionRepository->getById($id);
    }

    public function getByBrandAndModule(int $brandId, string $moduleKey): ?BrandModuleSubscription
    {
        return $this->subscriptionRepository->getByBrandAndModule($brandId, $moduleKey);
    }

    public function create(array $data): BrandModuleSubscription
    {
        return $this->subscriptionRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->subscriptionRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->subscriptionRepository->delete($id);
    }

    public function restore(int $id): bool
    {
        return $this->subscriptionRepository->restore($id);
    }

    public function getByBrand(int $brandId): Collection
    {
        return $this->subscriptionRepository->getByBrand($brandId);
    }

    public function getActiveSubscriptions(int $brandId): Collection
    {
        return $this->subscriptionRepository->getActiveSubscriptions($brandId);
    }

    public function toggleSubscriptionStatus(int $id): bool
    {
        return $this->subscriptionRepository->toggleSubscriptionStatus($id);
    }

    public function hasActiveSubscription(int $brandId, string $moduleKey): bool
    {
        return $this->subscriptionRepository->hasActiveSubscription($brandId, $moduleKey);
    }

    public function getDashboardStats(): array
    {
        return $this->subscriptionRepository->getDashboardStats();
    }

    /**
     * Subscribe brand to multiple modules.
     */
    public function subscribeToModules(int $brandId, array $moduleKeys, array $moduleConfigs = []): array
    {
        $results = [];

        DB::beginTransaction();

        try
        {
            // Fetch all available modules from landlord to get their IDs
            $landlordModules = $this->landlordService->getModules();
            $moduleIdMap = [];
            foreach ($landlordModules as $module) {
                $moduleIdMap[$module->module_key] = $module->id;
            }

            foreach ($moduleKeys as $moduleKey)
            {
                // Skip if module not found in landlord
                if (!isset($moduleIdMap[$moduleKey])) {
                    continue;
                }

                $subscriptionData = [
                    'brand_id' => $brandId,
                    'module_id' => $moduleIdMap[$moduleKey],
                    'module_key' => $moduleKey,
                    'status' => 'active',
                    'subscribed_at' => now(),
                    'module_config' => $moduleConfigs[$moduleKey] ?? null,
                ];

                $subscription = $this->create($subscriptionData);
                $results[] = $subscription;
            }

            DB::commit();
            return $results;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Unsubscribe brand from specific modules.
     */
    public function unsubscribeFromModules(int $brandId, array $moduleKeys): array
    {
        $results = [];

        DB::beginTransaction();

        try
        {
            foreach ($moduleKeys as $moduleKey)
            {
                $subscription = $this->getByBrandAndModule($brandId, $moduleKey);
                if ($subscription)
                {
                    $this->update($subscription->id, [
                        'status' => 'inactive',
                    ]);
                    $results[] = $subscription;
                }
            }

            DB::commit();
            return $results;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get available modules for subscription.
     */
    public function getAvailableModules(): array
    {
        return [
            'crm' => [
                'name' => translate('crm'),
                'description' => translate('customer_relationship_management'),
                'icon' => 'fas fa-handshake',
                'route' => '/crm/',
                'color' => 'primary',
            ],
            'hr' => [
                'name' => translate('hr'),
                'description' => translate('human_resources'),
                'icon' => 'fas fa-users',
                'route' => '/hr/',
                'color' => 'success',
            ],
            'accounting' => [
                'name' => translate('accounting'),
                'description' => translate('accounting_finance'),
                'icon' => 'fas fa-calculator',
                'route' => '/accounting/',
                'color' => 'info',
            ],
            'sales' => [
                'name' => translate('sales'),
                'description' => translate('manage_sales'),
                'icon' => 'fas fa-shopping-cart',
                'route' => '/sales/',
                'color' => 'warning',
            ],
            'inventory' => [
                'name' => translate('inventory'),
                'description' => translate('inventory_management'),
                'icon' => 'fas fa-boxes',
                'route' => '/inventory/',
                'color' => 'secondary',
            ],
            'reporting' => [
                'name' => translate('reporting'),
                'description' => translate('reports_analytics'),
                'icon' => 'fas fa-chart-bar',
                'route' => '/reporting/',
                'color' => 'dark',
            ],
        ];
    }

    /**
     * Get module information by key.
     */
    private function getModuleInfo(string $moduleKey): array
    {
        $modules = $this->getAvailableModules();
        return $modules[$moduleKey] ?? [
            'name' => ucfirst($moduleKey),
            'description' => ucfirst($moduleKey) . ' ' . translate('module'),
            'icon' => 'fas fa-cube',
            'route' => '/' . $moduleKey . '/',
            'color' => 'primary',
        ];
    }
}
