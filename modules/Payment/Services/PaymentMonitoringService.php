<?php

namespace Modules\Payment\Services;

use Modules\Payment\Entities\PaymentTransaction;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Entities\PaymentGatewayLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PaymentMonitoringService
{
    protected PaymentAnalyticsService $analyticsService;

    public function __construct(PaymentAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Monitor payment system health.
     *
     * @return array
     */
    public function getSystemHealth(): array
    {
        $health = [
            'overall_status' => 'healthy',
            'components' => [],
            'alerts' => [],
            'last_checked' => now()->toISOString(),
        ];

        // Check payment methods status
        $paymentMethodsHealth = $this->checkPaymentMethodsHealth();
        $health['components']['payment_methods'] = $paymentMethodsHealth;

        // Check transaction processing
        $transactionHealth = $this->checkTransactionHealth();
        $health['components']['transactions'] = $transactionHealth;

        // Check gateway connectivity
        $gatewayHealth = $this->checkGatewayHealth();
        $health['components']['gateways'] = $gatewayHealth;

        // Check database performance
        $databaseHealth = $this->checkDatabaseHealth();
        $health['components']['database'] = $databaseHealth;

        // Determine overall status
        $componentStatuses = collect($health['components'])->pluck('status');
        if ($componentStatuses->contains('critical')) {
            $health['overall_status'] = 'critical';
        } elseif ($componentStatuses->contains('warning')) {
            $health['overall_status'] = 'warning';
        }

        // Get active alerts
        $health['alerts'] = $this->analyticsService->checkAlertConditions();

        return $health;
    }

    /**
     * Check payment methods health.
     *
     * @return array
     */
    protected function checkPaymentMethodsHealth(): array
    {
        $totalMethods = PaymentMethod::count();
        $activeMethods = PaymentMethod::where('status', 'active')->count();
        $maintenanceMethods = PaymentMethod::where('status', 'maintenance')->count();

        $status = 'healthy';
        $issues = [];

        if ($activeMethods === 0) {
            $status = 'critical';
            $issues[] = 'No active payment methods available';
        } elseif ($activeMethods < 2) {
            $status = 'warning';
            $issues[] = 'Only one active payment method available';
        }

        if ($maintenanceMethods > 0) {
            $issues[] = "{$maintenanceMethods} payment methods in maintenance mode";
        }

        return [
            'status' => $status,
            'total_methods' => $totalMethods,
            'active_methods' => $activeMethods,
            'maintenance_methods' => $maintenanceMethods,
            'issues' => $issues,
        ];
    }

    /**
     * Check transaction processing health.
     *
     * @return array
     */
    protected function checkTransactionHealth(): array
    {
        $last15Minutes = [Carbon::now()->subMinutes(15), Carbon::now()];
        $lastHour = [Carbon::now()->subHour(), Carbon::now()];

        $recentTransactions = PaymentTransaction::whereBetween('created_at', $last15Minutes)->count();
        $recentSuccessful = PaymentTransaction::whereBetween('created_at', $last15Minutes)
                                            ->where('status', 'completed')
                                            ->count();
        
        $hourlyTransactions = PaymentTransaction::whereBetween('created_at', $lastHour)->count();
        $pendingTransactions = PaymentTransaction::where('status', 'pending')
                                               ->where('created_at', '<', Carbon::now()->subMinutes(5))
                                               ->count();

        $status = 'healthy';
        $issues = [];

        // Check success rate
        if ($recentTransactions > 0) {
            $successRate = ($recentSuccessful / $recentTransactions) * 100;
            if ($successRate < 80) {
                $status = 'critical';
                $issues[] = "Low success rate: {$successRate}%";
            } elseif ($successRate < 90) {
                $status = 'warning';
                $issues[] = "Moderate success rate: {$successRate}%";
            }
        }

        // Check for stuck transactions
        if ($pendingTransactions > 10) {
            $status = $status === 'critical' ? 'critical' : 'warning';
            $issues[] = "{$pendingTransactions} transactions stuck in pending state";
        }

        // Check processing volume
        $avgProcessingTime = PaymentTransaction::whereBetween('created_at', $last15Minutes)
                                             ->where('status', 'completed')
                                             ->avg('processing_time_ms');

        if ($avgProcessingTime > 10000) { // 10 seconds
            $status = $status === 'critical' ? 'critical' : 'warning';
            $issues[] = "High processing time: {$avgProcessingTime}ms";
        }

        return [
            'status' => $status,
            'recent_transactions' => $recentTransactions,
            'recent_successful' => $recentSuccessful,
            'success_rate' => $recentTransactions > 0 ? ($recentSuccessful / $recentTransactions) * 100 : 0,
            'hourly_transactions' => $hourlyTransactions,
            'pending_transactions' => $pendingTransactions,
            'avg_processing_time' => $avgProcessingTime,
            'issues' => $issues,
        ];
    }

    /**
     * Check gateway connectivity health.
     *
     * @return array
     */
    protected function checkGatewayHealth(): array
    {
        $last30Minutes = [Carbon::now()->subMinutes(30), Carbon::now()];
        
        $gatewayErrors = PaymentGatewayLog::whereBetween('created_at', $last30Minutes)
                                        ->where('log_level', 'error')
                                        ->count();

        $gatewayTimeouts = PaymentGatewayLog::whereBetween('created_at', $last30Minutes)
                                          ->where('processing_time_ms', '>', 30000) // 30 seconds
                                          ->count();

        $status = 'healthy';
        $issues = [];

        if ($gatewayErrors > 50) {
            $status = 'critical';
            $issues[] = "High gateway error rate: {$gatewayErrors} errors in 30 minutes";
        } elseif ($gatewayErrors > 20) {
            $status = 'warning';
            $issues[] = "Moderate gateway error rate: {$gatewayErrors} errors in 30 minutes";
        }

        if ($gatewayTimeouts > 10) {
            $status = $status === 'critical' ? 'critical' : 'warning';
            $issues[] = "Gateway timeout issues: {$gatewayTimeouts} timeouts in 30 minutes";
        }

        return [
            'status' => $status,
            'recent_errors' => $gatewayErrors,
            'recent_timeouts' => $gatewayTimeouts,
            'issues' => $issues,
        ];
    }

    /**
     * Check database performance.
     *
     * @return array
     */
    protected function checkDatabaseHealth(): array
    {
        $start = microtime(true);
        
        try {
            // Simple query to test database responsiveness
            PaymentTransaction::count();
            $queryTime = (microtime(true) - $start) * 1000;

            $status = 'healthy';
            $issues = [];

            if ($queryTime > 1000) { // 1 second
                $status = 'critical';
                $issues[] = "Database query time too high: {$queryTime}ms";
            } elseif ($queryTime > 500) { // 500ms
                $status = 'warning';
                $issues[] = "Database query time elevated: {$queryTime}ms";
            }

            return [
                'status' => $status,
                'query_time_ms' => round($queryTime, 2),
                'connection_status' => 'connected',
                'issues' => $issues,
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'critical',
                'query_time_ms' => null,
                'connection_status' => 'failed',
                'issues' => ['Database connection failed: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Get performance metrics.
     *
     * @return array
     */
    public function getPerformanceMetrics(): array
    {
        $cacheKey = 'payment_performance_metrics';
        
        return Cache::remember($cacheKey, 300, function () { // Cache for 5 minutes
            $last24Hours = [Carbon::now()->subDay(), Carbon::now()];
            
            $metrics = [
                'throughput' => $this->calculateThroughput(),
                'latency' => $this->calculateLatency(),
                'error_rates' => $this->calculateErrorRates(),
                'gateway_performance' => $this->getGatewayPerformance(),
                'resource_usage' => $this->getResourceUsage(),
            ];

            return $metrics;
        });
    }

    /**
     * Calculate transaction throughput.
     *
     * @return array
     */
    protected function calculateThroughput(): array
    {
        $last24Hours = [Carbon::now()->subDay(), Carbon::now()];
        $lastHour = [Carbon::now()->subHour(), Carbon::now()];
        
        $transactions24h = PaymentTransaction::whereBetween('created_at', $last24Hours)->count();
        $transactionsHour = PaymentTransaction::whereBetween('created_at', $lastHour)->count();
        
        return [
            'transactions_per_second_24h' => round($transactions24h / (24 * 3600), 2),
            'transactions_per_minute_1h' => round($transactionsHour / 60, 2),
            'peak_hour_tps' => $this->getPeakHourTPS(),
        ];
    }

    /**
     * Calculate processing latency.
     *
     * @return array
     */
    protected function calculateLatency(): array
    {
        $last24Hours = [Carbon::now()->subDay(), Carbon::now()];
        
        $latencyStats = PaymentTransaction::whereBetween('created_at', $last24Hours)
                                        ->where('status', 'completed')
                                        ->selectRaw('
                                            AVG(processing_time_ms) as avg_latency,
                                            MIN(processing_time_ms) as min_latency,
                                            MAX(processing_time_ms) as max_latency,
                                            PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY processing_time_ms) as p50_latency,
                                            PERCENTILE_CONT(0.95) WITHIN GROUP (ORDER BY processing_time_ms) as p95_latency,
                                            PERCENTILE_CONT(0.99) WITHIN GROUP (ORDER BY processing_time_ms) as p99_latency
                                        ')
                                        ->first();

        return [
            'avg_latency_ms' => round($latencyStats->avg_latency ?? 0, 2),
            'min_latency_ms' => $latencyStats->min_latency ?? 0,
            'max_latency_ms' => $latencyStats->max_latency ?? 0,
            'p50_latency_ms' => round($latencyStats->p50_latency ?? 0, 2),
            'p95_latency_ms' => round($latencyStats->p95_latency ?? 0, 2),
            'p99_latency_ms' => round($latencyStats->p99_latency ?? 0, 2),
        ];
    }

    /**
     * Calculate error rates.
     *
     * @return array
     */
    protected function calculateErrorRates(): array
    {
        $last24Hours = [Carbon::now()->subDay(), Carbon::now()];
        
        $totalTransactions = PaymentTransaction::whereBetween('created_at', $last24Hours)->count();
        $failedTransactions = PaymentTransaction::whereBetween('created_at', $last24Hours)
                                              ->where('status', 'failed')
                                              ->count();
        
        $errorRate = $totalTransactions > 0 ? ($failedTransactions / $totalTransactions) * 100 : 0;
        
        return [
            'error_rate_24h' => round($errorRate, 2),
            'total_errors_24h' => $failedTransactions,
            'error_rate_by_gateway' => $this->getErrorRatesByGateway(),
        ];
    }

    /**
     * Get gateway performance metrics.
     *
     * @return array
     */
    protected function getGatewayPerformance(): array
    {
        $last24Hours = [Carbon::now()->subDay(), Carbon::now()];
        
        return PaymentMethod::with(['transactions' => function ($query) use ($last24Hours) {
            $query->whereBetween('created_at', $last24Hours);
        }])->get()->map(function ($method) {
            $transactions = $method->transactions;
            $totalTransactions = $transactions->count();
            $successfulTransactions = $transactions->where('status', 'completed')->count();
            $avgLatency = $transactions->where('status', 'completed')->avg('processing_time_ms');
            
            return [
                'gateway' => $method->processor_type,
                'name' => $method->name,
                'transactions' => $totalTransactions,
                'success_rate' => $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions) * 100 : 0,
                'avg_latency_ms' => round($avgLatency ?? 0, 2),
                'status' => $method->status,
            ];
        })->toArray();
    }

    /**
     * Get resource usage metrics.
     *
     * @return array
     */
    protected function getResourceUsage(): array
    {
        return [
            'database_connections' => $this->getDatabaseConnections(),
            'memory_usage' => $this->getMemoryUsage(),
            'cache_hit_rate' => $this->getCacheHitRate(),
        ];
    }

    /**
     * Get peak hour transactions per second.
     *
     * @return float
     */
    protected function getPeakHourTPS(): float
    {
        $last24Hours = Carbon::now()->subDay();
        $peakHour = 0;
        
        for ($i = 0; $i < 24; $i++) {
            $hourStart = $last24Hours->copy()->addHours($i);
            $hourEnd = $hourStart->copy()->addHour();
            
            $hourlyTransactions = PaymentTransaction::whereBetween('created_at', [$hourStart, $hourEnd])->count();
            $tps = $hourlyTransactions / 3600;
            
            if ($tps > $peakHour) {
                $peakHour = $tps;
            }
        }
        
        return round($peakHour, 2);
    }

    /**
     * Get error rates by gateway.
     *
     * @return array
     */
    protected function getErrorRatesByGateway(): array
    {
        $last24Hours = [Carbon::now()->subDay(), Carbon::now()];
        
        return PaymentMethod::with(['transactions' => function ($query) use ($last24Hours) {
            $query->whereBetween('created_at', $last24Hours);
        }])->get()->map(function ($method) {
            $transactions = $method->transactions;
            $totalTransactions = $transactions->count();
            $failedTransactions = $transactions->where('status', 'failed')->count();
            
            return [
                'gateway' => $method->processor_type,
                'error_rate' => $totalTransactions > 0 ? ($failedTransactions / $totalTransactions) * 100 : 0,
                'total_errors' => $failedTransactions,
            ];
        })->toArray();
    }

    /**
     * Get database connections count.
     *
     * @return int
     */
    protected function getDatabaseConnections(): int
    {
        try {
            $result = \DB::select('SHOW STATUS WHERE Variable_name = "Threads_connected"');
            return $result[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get memory usage.
     *
     * @return array
     */
    protected function getMemoryUsage(): array
    {
        return [
            'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ];
    }

    /**
     * Get cache hit rate.
     *
     * @return float
     */
    protected function getCacheHitRate(): float
    {
        // This would depend on your cache implementation
        // For Redis, you could use INFO stats
        return 95.0; // Placeholder
    }

    /**
     * Log system health check.
     *
     * @return void
     */
    public function logHealthCheck(): void
    {
        $health = $this->getSystemHealth();
        
        if ($health['overall_status'] === 'critical') {
            Log::critical('Payment system health check failed', $health);
        } elseif ($health['overall_status'] === 'warning') {
            Log::warning('Payment system health check has warnings', $health);
        } else {
            Log::info('Payment system health check passed', [
                'status' => $health['overall_status'],
                'components_count' => count($health['components']),
                'alerts_count' => count($health['alerts']),
            ]);
        }
    }
}
