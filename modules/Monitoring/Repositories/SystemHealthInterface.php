<?php

namespace Modules\Monitoring\Repositories;

interface SystemHealthInterface
{
    public function getOverview();
    public function getTenantHealth($tenantId);
    public function getRealTimeData();
    public function getUptimeStats();
    public function getDatabaseHealth();
    public function getQueueStats();
}
