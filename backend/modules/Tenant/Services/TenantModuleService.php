<?php

namespace Modules\Tenant\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Customer\Entities\Tenant\BrandModule;
use Modules\Utilities\Entities\Module;

class TenantModuleService
{
    /**
     * Get the current tenant's subscribed modules with summary data.
     *
     * Returns each active module with:
     * - Module details from landlord DB (name, description, icon, route, slogan)
     * - Color palette from tenant brand_modules
     * - Unread notifications count for the module
     * - Open tickets count for the module
     */
    public function getSubscribedModules(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        // Get active brand modules for brands the user belongs to
        $brandIds = $this->getUserBrandIds($user);

        if (empty($brandIds)) {
            return [];
        }

        $brandModules = BrandModule::with(['brand'])
            ->whereIn('brand_id', $brandIds)
            ->active()
            ->get();

        if ($brandModules->isEmpty()) {
            return $this->getLegacySubscribedModules($brandIds);
        }

        // Get landlord module details in one query
        $moduleIds = $brandModules->pluck('module_id')->filter()->unique()->values()->toArray();
        $moduleKeys = $brandModules->pluck('module_key')->filter()->unique()->values()->toArray();
        $landlordModules = Module::on('landlord')
            ->where(function ($query) use ($moduleIds, $moduleKeys) {
                if (!empty($moduleIds)) {
                    $query->whereIn('id', $moduleIds);
                }
                if (!empty($moduleKeys)) {
                    $query->orWhereIn('module_key', $moduleKeys);
                }
            })
            ->where('status', 'active')
            ->get()
            ->keyBy('id');
        $landlordModulesByKey = $landlordModules->keyBy('module_key');

        // Get notification counts per module_id
        $resolvedModuleIds = $landlordModules->pluck('id')->unique()->values()->toArray();
        $notificationCounts = DB::table('notifications')
            ->where('is_read', false)
            ->when(!empty($resolvedModuleIds), function ($query) use ($resolvedModuleIds) {
                $query->whereIn('module_id', $resolvedModuleIds);
            })
            ->select('module_id', DB::raw('count(*) as count'))
            ->groupBy('module_id')
            ->pluck('count', 'module_id')
            ->toArray();

        // Get open ticket counts per brand (tickets don't have module_id, so we count per brand)
        $ticketCounts = DB::table('tickets')
            ->whereIn('brand_id', $brandIds)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->select('brand_id', DB::raw('count(*) as count'))
            ->groupBy('brand_id')
            ->pluck('count', 'brand_id')
            ->toArray();

        $result = [];
        foreach ($brandModules as $brandModule) {
            $landlordModule = $landlordModules->get($brandModule->module_id)
                ?? $landlordModulesByKey->get($brandModule->module_key);
            if (!$landlordModule) {
                continue;
            }

            $result[] = [
                'id' => $brandModule->id,
                'module_id' => $landlordModule->id,
                'module_key' => $landlordModule->module_key ?? $brandModule->module_key,
                'name' => $landlordModule->name,
                'description' => $landlordModule->description,
                'icon' => $landlordModule->icon,
                'route' => $landlordModule->route,
                'slogan' => $landlordModule->slogan,
                'navigation' => $landlordModule->navigation,
                'status' => $brandModule->status,
                'brand_id' => $brandModule->brand_id,
                'brand_name' => $brandModule->brand?->name,
                'brand_slug' => $brandModule->brand?->slug,
                'color_palette' => $brandModule->color_palette,
                'subscribed_at' => $brandModule->subscribed_at?->toIso8601String(),
                'unread_notifications' => $notificationCounts[$landlordModule->id] ?? 0,
                'open_tickets' => $ticketCounts[$brandModule->brand_id] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Get a single subscribed module by module_key.
     */
    public function getSubscribedModule(string $moduleKey): ?array
    {
        $modules = $this->getSubscribedModules();
        foreach ($modules as $module) {
            if ($module['module_key'] === $moduleKey) {
                return $module;
            }
        }
        return null;
    }

    /**
     * Get the brand IDs the user has access to.
     */
    private function getUserBrandIds($user): array
    {
        $brandIds = [];

        // Prefer explicit single-brand mapping if present.
        if (isset($user->brand_id) && !empty($user->brand_id)) {
            $brandIds[] = (int) $user->brand_id;
        }

        // Use many-to-many relation when available.
        if (method_exists($user, 'brands')) {
            try {
                $relatedBrandIds = $user->brands()->pluck('brands.id')->map(fn ($id) => (int) $id)->toArray();
                $brandIds = array_merge($brandIds, $relatedBrandIds);
            } catch (\Throwable $e) {
                // Ignore relation mismatches and continue to fallback.
            }
        }

        $brandIds = array_values(array_unique(array_filter($brandIds)));
        if (!empty($brandIds)) {
            return $brandIds;
        }

        // Fallback for legacy setups: expose all active brands.
        // Some tenants still use `is_active` instead of `status`.
        $brandsQuery = DB::table('brands');

        if (Schema::hasColumn('brands', 'status')) {
            $brandsQuery->where('status', 'active');
        } elseif (Schema::hasColumn('brands', 'is_active')) {
            $brandsQuery->where('is_active', true);
        }

        return $brandsQuery
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    /**
     * Legacy fallback: read old brand_module pivot records.
     */
    private function getLegacySubscribedModules(array $brandIds): array
    {
        if (!DB::getSchemaBuilder()->hasTable('brand_module')) {
            return [];
        }

        $legacyRows = DB::table('brand_module')
            ->whereIn('brand_id', $brandIds)
            ->get(['id', 'brand_id', 'module_id', 'created_at']);

        if ($legacyRows->isEmpty()) {
            return [];
        }

        $moduleIds = $legacyRows->pluck('module_id')->filter()->unique()->values()->toArray();
        if (empty($moduleIds)) {
            return [];
        }

        $landlordModules = Module::on('landlord')
            ->whereIn('id', $moduleIds)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        if ($landlordModules->isEmpty()) {
            return [];
        }

        $brandData = DB::table('brands')
            ->whereIn('id', $brandIds)
            ->select(['id', 'name', 'slug'])
            ->get()
            ->keyBy('id');

        $notificationCounts = DB::table('notifications')
            ->where('is_read', false)
            ->whereIn('module_id', $landlordModules->pluck('id')->toArray())
            ->select('module_id', DB::raw('count(*) as count'))
            ->groupBy('module_id')
            ->pluck('count', 'module_id')
            ->toArray();

        $ticketCounts = DB::table('tickets')
            ->whereIn('brand_id', $brandIds)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->select('brand_id', DB::raw('count(*) as count'))
            ->groupBy('brand_id')
            ->pluck('count', 'brand_id')
            ->toArray();

        $result = [];
        foreach ($legacyRows as $row) {
            $landlordModule = $landlordModules->get($row->module_id);
            if (!$landlordModule) {
                continue;
            }

            $result[] = [
                'id' => $row->id,
                'module_id' => $landlordModule->id,
                'module_key' => $landlordModule->module_key,
                'name' => $landlordModule->name,
                'description' => $landlordModule->description,
                'icon' => $landlordModule->icon,
                'route' => $landlordModule->route,
                'slogan' => $landlordModule->slogan,
                'navigation' => $landlordModule->navigation,
                'status' => 'active',
                'brand_id' => $row->brand_id,
                'brand_name' => $brandData[$row->brand_id]?->name ?? null,
                'brand_slug' => $brandData[$row->brand_id]?->slug ?? null,
                'color_palette' => null,
                'subscribed_at' => $row->created_at,
                'unread_notifications' => $notificationCounts[$landlordModule->id] ?? 0,
                'open_tickets' => $ticketCounts[$row->brand_id] ?? 0,
            ];
        }

        return $result;
    }
}
