<?php

namespace Modules\Monitoring\Repositories;

interface ErrorManagementInterface
{
    public function getOverview();
    public function getTenantErrors($tenantId);
    public function getRealTimeData();
    public function getErrorLogs($tenantId = null, $filters = []);
    public function getRecurringErrors();
    public function getErrorAlerts();
    public function getErrorTrends();
}
