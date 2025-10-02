<?php

namespace Modules\Monitoring\Repositories;

interface ResourceInsightsInterface
{
    public function getOverview();
    public function getTenantResources($tenantId);
    public function getRealTimeData();
    public function getDatabaseSizeHistory();
    public function getStorageUsage();
    public function getRateLimitStats();
}
