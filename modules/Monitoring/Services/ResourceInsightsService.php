<?php

namespace Modules\Monitoring\Services;

use Modules\Monitoring\Repositories\ResourceInsightsInterface;

class ResourceInsightsService
{
    protected $repository;

    public function __construct(ResourceInsightsInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getOverview()
    {
        return [
            'total_storage_gb' => rand(100, 500),
            'database_size_gb' => rand(50, 200),
            'average_db_growth' => rand(5, 20), // MB per day
            'storage_alerts' => rand(0, 3),
        ];
    }

    public function getTenantResources($tenantId)
    {
        return [
            'tenant_id' => $tenantId,
            'database_size_mb' => rand(50, 500),
            'storage_usage_mb' => rand(100, 1000),
            'growth_rate' => rand(1, 10), // MB per day
        ];
    }

    public function getRealTimeData()
    {
        return [
            'timestamp' => now()->toISOString(),
            'cpu_usage' => rand(20, 80),
            'memory_usage' => rand(30, 90),
            'disk_usage' => rand(40, 85),
        ];
    }
}
