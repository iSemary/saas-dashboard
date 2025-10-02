<?php

namespace Modules\Monitoring\Services;

use Modules\Monitoring\Repositories\ErrorManagementInterface;

class ErrorManagementService
{
    protected $repository;

    public function __construct(ErrorManagementInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getOverview()
    {
        return [
            'total_errors_today' => rand(50, 200),
            'critical_errors' => rand(5, 20),
            'error_rate' => rand(2, 8),
            'most_common_errors' => [
                'Database connection timeout',
                'API rate limit exceeded',
                'File not found',
            ],
        ];
    }

    public function getTenantErrors($tenantId)
    {
        return [
            'tenant_id' => $tenantId,
            'errors_today' => rand(5, 50),
            'error_rate' => rand(1, 10),
            'recent_errors' => $this->getRecentErrors($tenantId),
        ];
    }

    public function getRealTimeData()
    {
        return [
            'timestamp' => now()->toISOString(),
            'errors_per_minute' => rand(1, 10),
            'critical_errors' => rand(0, 3),
            'error_rate' => rand(1, 5),
        ];
    }

    private function getRecentErrors($tenantId)
    {
        return [
            [
                'error' => 'Database query timeout',
                'level' => 'error',
                'count' => rand(1, 10),
                'last_occurred' => now()->subMinutes(rand(5, 60)),
            ],
            [
                'error' => 'API authentication failed',
                'level' => 'warning',
                'count' => rand(1, 5),
                'last_occurred' => now()->subMinutes(rand(10, 120)),
            ],
        ];
    }
}
