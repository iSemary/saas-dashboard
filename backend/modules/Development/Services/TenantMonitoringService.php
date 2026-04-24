<?php

namespace Modules\Development\Services;

use Modules\Tenant\Entities\Tenant;

class TenantMonitoringService
{
    public function getTenantStatus(): array
    {
        $tenants = Tenant::all();
        $result = [];
        foreach ($tenants as $tenant) {
            $result[] = [
                'tenant' => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'domain' => $tenant->domain ?? '',
                ],
                'status' => ($tenant->is_active ?? true) ? 'healthy' : 'warning',
                'last_activity' => $tenant->updated_at?->toIso8601String(),
            ];
        }
        return $result;
    }
}
