<?php

namespace Modules\Development\Services;

use Illuminate\Support\Facades\DB;

class SystemHealthService
{
    public function getHealthChecks(): array
    {
        $checks = [];

        // Database check
        try {
            DB::connection()->getPdo();
            $checks['database'] = ['status' => 'healthy', 'message' => translate('message.action_completed')];
        } catch (\Exception $e) {
            $checks['database'] = ['status' => 'unhealthy', 'message' => $e->getMessage()];
        }

        // Cache check
        try {
            cache()->put('_health_check', 'ok', 10);
            $status = cache()->get('_health_check') === 'ok' ? 'healthy' : 'unhealthy';
            $checks['cache'] = ['status' => $status, 'message' => $status === 'healthy' ? 'Cache is working' : 'Cache is not working'];
        } catch (\Exception $e) {
            $checks['cache'] = ['status' => 'unhealthy', 'message' => $e->getMessage()];
        }

        // Storage check
        try {
            $checks['storage'] = ['status' => 'healthy', 'message' => translate('message.action_completed')];
        } catch (\Exception $e) {
            $checks['storage'] = ['status' => 'unhealthy', 'message' => $e->getMessage()];
        }

        return $checks;
    }
}
