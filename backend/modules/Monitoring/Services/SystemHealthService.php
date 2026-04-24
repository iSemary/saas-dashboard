<?php

namespace Modules\Monitoring\Services;

use Modules\Monitoring\Repositories\SystemHealthInterface;

class SystemHealthService
{
    protected $repository;

    public function __construct(SystemHealthInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getOverview()
    {
        return $this->repository->getOverview();
    }

    public function getTenantHealth($tenantId)
    {
        return $this->repository->getTenantHealth($tenantId);
    }

    public function getRealTimeData()
    {
        return $this->repository->getRealTimeData();
    }

    public function getUptimeStats()
    {
        return $this->repository->getUptimeStats();
    }

    public function getDatabaseHealth()
    {
        return $this->repository->getDatabaseHealth();
    }

    public function getQueueStats()
    {
        return $this->repository->getQueueStats();
    }
}
