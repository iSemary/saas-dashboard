<?php

namespace Modules\Monitoring\Services;

use Modules\Monitoring\Repositories\TenantBehaviorInterface;

class TenantBehaviorService
{
    protected $repository;

    public function __construct(TenantBehaviorInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getOverview()
    {
        return $this->repository->getOverview();
    }

    public function getTenantBehavior($tenantId)
    {
        return $this->repository->getTenantBehavior($tenantId);
    }

    public function getRealTimeData()
    {
        return $this->repository->getRealTimeData();
    }

    public function getActiveSessions()
    {
        return $this->repository->getActiveSessions();
    }

    public function getLoginActivity()
    {
        return $this->repository->getLoginActivity();
    }

    public function getApiUsage()
    {
        return $this->repository->getApiUsage();
    }

    public function getFeatureUsage()
    {
        return $this->repository->getFeatureUsage();
    }

    public function getUnusualActivity()
    {
        return $this->repository->getUnusualActivity();
    }
}
