<?php

namespace Modules\Tenant\Services;

use Illuminate\Support\Facades\DB;
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
            return [];
        }

        // Get landlord module details in one query
        $moduleIds = $brandModules->pluck('module_id')->unique()->toArray();
        $landlordModules = Module::on('landlord')
            ->whereIn('id', $moduleIds)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        // Get notification counts per module_id
        $notificationCounts = DB::table('notifications')
            ->where('is_read', false)
            ->whereIn('module_id', $moduleIds)
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
            $landlordModule = $landlordModules->get($brandModule->module_id);
            if (!$landlordModule) {
                continue;
            }

            $result[] = [
                'id' => $brandModule->id,
                'module_id' => $brandModule->module_id,
                'module_key' => $brandModule->module_key,
                'name' => $landlordModule->name,
                'description' => $landlordModule->description,
                'icon' => $landlordModule->icon,
                'route' => $landlordModule->route,
                'slogan' => $landlordModule->slogan,
                'navigation' => $landlordModule->navigation,
                'status' => $brandModule->status,
                'brand_id' => $brandModule->brand_id,
                'brand_name' => $brandModule->brand?->name,
                'color_palette' => $brandModule->color_palette,
                'subscribed_at' => $brandModule->subscribed_at?->toIso8601String(),
                'unread_notifications' => $notificationCounts[$brandModule->module_id] ?? 0,
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
        // If user has a brand_id field, use it
        if (method_exists($user, 'brands')) {
            return $user->brands()->pluck('brands.id')->toArray();
        }

        // Fallback: get all active brands
        return DB::table('brands')
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();
    }
}
