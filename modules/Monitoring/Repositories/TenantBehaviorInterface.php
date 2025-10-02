<?php

namespace Modules\Monitoring\Repositories;

interface TenantBehaviorInterface
{
    public function getOverview();
    public function getTenantBehavior($tenantId);
    public function getRealTimeData();
    public function getActiveSessions();
    public function getLoginActivity();
    public function getApiUsage();
    public function getFeatureUsage();
    public function getUnusualActivity();
}
