<?php

namespace Modules\Monitoring\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Modules\Tenant\Entities\Tenant;

class MonitoringApiController extends ApiController
{
    /**
     * Monitoring dashboard: grouped metrics (no placeholder/random data).
     */
    public function overview(): JsonResponse
    {
        $dbStatus = 'error';
        $connections = 0;
        try {
            DB::connection()->getPdo();
            $dbStatus = 'healthy';
            $result = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            $connections = isset($result[0]->Value) ? (int) $result[0]->Value : 0;
        } catch (\Throwable $e) {
            $dbStatus = 'error';
        }

        $pendingJobs = 0;
        $failedJobs = 0;
        try {
            $pendingJobs = (int) DB::table('jobs')->count();
        } catch (\Throwable) {
        }
        try {
            $failedJobs = (int) DB::table('failed_jobs')->count();
        } catch (\Throwable) {
        }

        $payload = [
            'database' => [
                'connections' => $connections,
                'status' => $dbStatus,
            ],
            'queue' => [
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
            ],
            'tenants' => [
                'total' => (int) Tenant::count(),
                'active_24h' => (int) Tenant::where('updated_at', '>=', now()->subDay())->count(),
            ],
        ];

        return $this->respondWithArray($payload, translate('Monitoring overview retrieved'));
    }

    public function systemHealth(): JsonResponse
    {
        $checks = [];

        try {
            DB::connection()->getPdo();
            $checks[] = [
                'name' => 'database',
                'status' => 'ok',
                'message' => translate('Database connection is healthy'),
            ];
        } catch (\Throwable $e) {
            $checks[] = [
                'name' => 'database',
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        try {
            $key = '__landlord_health_' . uniqid('', true);
            Cache::put($key, 1, 5);
            $ok = Cache::get($key) === 1;
            Cache::forget($key);
            $checks[] = [
                'name' => 'cache',
                'status' => $ok ? 'ok' : 'error',
                'message' => $ok ? translate('Cache read/write OK') : translate('Cache read/write failed'),
            ];
        } catch (\Throwable $e) {
            $checks[] = [
                'name' => 'cache',
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        $failedJobs = 0;
        try {
            $failedJobs = (int) DB::table('failed_jobs')->count();
        } catch (\Throwable) {
        }
        $queueStatus = $failedJobs > 50 ? 'warning' : 'ok';
        $checks[] = [
            'name' => 'queue',
            'status' => $queueStatus,
            'message' => $failedJobs > 50
                ? translate('High number of failed jobs') . " ({$failedJobs})"
                : translate('Queue tables reachable'),
            'details' => [
                'failed_jobs' => (string) $failedJobs,
            ],
        ];

        try {
            $path = '.landlord-health-' . uniqid('', true);
            Storage::disk('local')->put($path, '1');
            $exists = Storage::disk('local')->exists($path);
            Storage::disk('local')->delete($path);
            $checks[] = [
                'name' => 'storage',
                'status' => $exists ? 'ok' : 'error',
                'message' => $exists ? translate('Local storage writable') : translate('Local storage check failed'),
            ];
        } catch (\Throwable $e) {
            $checks[] = [
                'name' => 'storage',
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        return $this->respondWithArray($checks, translate('System health checks retrieved'));
    }

    /**
     * Per-tenant snapshot: DB size from information_schema, user count when tenant DB is reachable.
     */
    public function tenantMonitoring(): JsonResponse
    {
        $items = [];

        foreach (Tenant::query()->orderBy('id')->get() as $tenant) {
            $sizeMb = $this->tenantDatabaseSizeMb($tenant->database);
            $userCount = $this->tenantUserCount($tenant->database);

            $status = 'healthy';
            if ($sizeMb === null && $userCount === null) {
                $status = 'warning';
            }

            $items[] = [
                'tenant' => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'domain' => $tenant->domain,
                ],
                'status' => $status,
                'database_size' => $sizeMb !== null ? number_format((float) $sizeMb, 2) . ' MB' : null,
                'user_count' => $userCount,
                'last_activity' => $tenant->updated_at?->toIso8601String(),
            ];
        }

        config(['database.connections.tenant.database' => null]);
        DB::purge('tenant');

        return $this->respondWithArray($items, translate('Tenant monitoring data retrieved'));
    }

    private function tenantDatabaseSizeMb(?string $dbName): ?float
    {
        if ($dbName === null || $dbName === '') {
            return null;
        }

        try {
            $rows = DB::select(
                'SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                 FROM information_schema.tables
                 WHERE table_schema = ?',
                [$dbName]
            );

            return isset($rows[0]->size_mb) ? (float) $rows[0]->size_mb : 0.0;
        } catch (\Throwable) {
            return null;
        }
    }

    private function tenantUserCount(?string $dbName): ?int
    {
        if ($dbName === null || $dbName === '') {
            return null;
        }

        try {
            config(['database.connections.tenant.database' => $dbName]);
            DB::purge('tenant');
            DB::reconnect('tenant');

            return (int) DB::connection('tenant')->table('users')->count();
        } catch (\Throwable) {
            return null;
        }
    }
}
