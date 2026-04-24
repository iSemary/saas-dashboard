<?php

namespace Modules\Development\Services;

use Illuminate\Support\Facades\DB;

class MonitoringService
{
    public function getDashboardData(): array
    {
        return [
            'server' => [
                'php_version' => phpversion(),
                'laravel_version' => app()->version(),
                'environment' => app()->environment(),
                'debug_mode' => config('app.debug'),
            ],
            'database' => [
                'driver' => config('database.default'),
                'connections' => $this->getDatabaseStatus(),
            ],
            'cache' => [
                'driver' => config('cache.default'),
                'status' => $this->getCacheStatus(),
            ],
            'queue' => [
                'driver' => config('queue.default'),
                'failed_jobs' => DB::table('failed_jobs')->count(),
            ],
        ];
    }

    protected function getDatabaseStatus(): string
    {
        try {
            DB::connection()->getPdo();
            return 'connected';
        } catch (\Exception $e) {
            return 'disconnected';
        }
    }

    protected function getCacheStatus(): string
    {
        try {
            cache()->put('_health_check', 'ok', 10);
            return cache()->get('_health_check') === 'ok' ? 'working' : 'not_working';
        } catch (\Exception $e) {
            return 'not_working';
        }
    }
}
