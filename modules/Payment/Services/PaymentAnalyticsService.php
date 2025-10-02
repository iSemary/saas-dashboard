<?php

namespace Modules\Payment\Services;

use Modules\Payment\Entities\PaymentTransaction;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Payment\Entities\Refund;
use Modules\Payment\Entities\Chargeback;
use Modules\Payment\Entities\PaymentGatewayLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentAnalyticsService
{
    /**
     * Get payment overview statistics.
     *
     * @param string $period
     * @return array
     */
    public function getPaymentOverview(string $period = '30d'): array
    {
        $dateRange = $this->getDateRange($period);
        
        $totalTransactions = PaymentTransaction::whereBetween('created_at', $dateRange)->count();
        $successfulTransactions = PaymentTransaction::whereBetween('created_at', $dateRange)
                                                  ->where('status', 'completed')
                                                  ->count();
        
        $totalVolume = PaymentTransaction::whereBetween('created_at', $dateRange)
                                       ->where('status', 'completed')
                                       ->sum('amount');
        
        $totalRefunds = Refund::whereBetween('created_at', $dateRange)->sum('amount');
        $totalChargebacks = Chargeback::whereBetween('created_at', $dateRange)->sum('amount');
        
        $successRate = $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions) * 100 : 0;
        
        return [
            'total_transactions' => $totalTransactions,
            'successful_transactions' => $successfulTransactions,
            'success_rate' => round($successRate, 2),
            'total_volume' => $totalVolume,
            'total_refunds' => $totalRefunds,
            'total_chargebacks' => $totalChargebacks,
            'net_volume' => $totalVolume - $totalRefunds - $totalChargebacks,
            'period' => $period,
            'date_range' => $dateRange,
        ];
    }

    /**
     * Get payment method performance analytics.
     *
     * @param string $period
     * @return array
     */
    public function getPaymentMethodPerformance(string $period = '30d'): array
    {
        $dateRange = $this->getDateRange($period);
        
        $performance = PaymentMethod::with(['transactions' => function ($query) use ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        }])->get()->map(function ($method) {
            $transactions = $method->transactions;
            $totalTransactions = $transactions->count();
            $successfulTransactions = $transactions->where('status', 'completed')->count();
            $totalVolume = $transactions->where('status', 'completed')->sum('amount');
            $avgProcessingTime = $transactions->avg('processing_time_ms') ?? 0;
            
            return [
                'id' => $method->id,
                'name' => $method->name,
                'processor_type' => $method->processor_type,
                'total_transactions' => $totalTransactions,
                'successful_transactions' => $successfulTransactions,
                'success_rate' => $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions) * 100 : 0,
                'total_volume' => $totalVolume,
                'avg_processing_time' => round($avgProcessingTime, 2),
                'status' => $method->status,
            ];
        })->sortByDesc('total_volume')->values();

        return $performance->toArray();
    }

    /**
     * Get transaction trends over time.
     *
     * @param string $period
     * @param string $groupBy
     * @return array
     */
    public function getTransactionTrends(string $period = '30d', string $groupBy = 'day'): array
    {
        $dateRange = $this->getDateRange($period);
        $dateFormat = $this->getDateFormat($groupBy);
        
        $trends = PaymentTransaction::whereBetween('created_at', $dateRange)
                                  ->select(
                                      DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
                                      DB::raw('COUNT(*) as total_transactions'),
                                      DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as successful_transactions'),
                                      DB::raw('SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_volume'),
                                      DB::raw('AVG(CASE WHEN status = "completed" THEN processing_time_ms ELSE NULL END) as avg_processing_time')
                                  )
                                  ->groupBy('period')
                                  ->orderBy('period')
                                  ->get()
                                  ->map(function ($item) {
                                      $item->success_rate = $item->total_transactions > 0 
                                          ? ($item->successful_transactions / $item->total_transactions) * 100 
                                          : 0;
                                      return $item;
                                  });

        return $trends->toArray();
    }

    /**
     * Get currency distribution analytics.
     *
     * @param string $period
     * @return array
     */
    public function getCurrencyDistribution(string $period = '30d'): array
    {
        $dateRange = $this->getDateRange($period);
        
        $distribution = PaymentTransaction::whereBetween('created_at', $dateRange)
                                        ->where('status', 'completed')
                                        ->join('currencies', 'payment_transactions.currency_id', '=', 'currencies.id')
                                        ->select(
                                            'currencies.code',
                                            'currencies.name',
                                            DB::raw('COUNT(*) as transaction_count'),
                                            DB::raw('SUM(amount) as total_volume'),
                                            DB::raw('AVG(amount) as avg_transaction_amount')
                                        )
                                        ->groupBy('currencies.id', 'currencies.code', 'currencies.name')
                                        ->orderByDesc('total_volume')
                                        ->get();

        $totalVolume = $distribution->sum('total_volume');
        
        return $distribution->map(function ($item) use ($totalVolume) {
            $item->percentage = $totalVolume > 0 ? ($item->total_volume / $totalVolume) * 100 : 0;
            return $item;
        })->toArray();
    }

    /**
     * Get failure analysis.
     *
     * @param string $period
     * @return array
     */
    public function getFailureAnalysis(string $period = '30d'): array
    {
        $dateRange = $this->getDateRange($period);
        
        $failures = PaymentTransaction::whereBetween('created_at', $dateRange)
                                    ->where('status', 'failed')
                                    ->select(
                                        DB::raw('JSON_EXTRACT(error_details, "$.code") as error_code'),
                                        DB::raw('JSON_EXTRACT(error_details, "$.message") as error_message'),
                                        DB::raw('COUNT(*) as failure_count')
                                    )
                                    ->groupBy('error_code', 'error_message')
                                    ->orderByDesc('failure_count')
                                    ->limit(10)
                                    ->get();

        $failuresByGateway = PaymentTransaction::whereBetween('created_at', $dateRange)
                                             ->where('status', 'failed')
                                             ->join('payment_methods', 'payment_transactions.payment_method_id', '=', 'payment_methods.id')
                                             ->select(
                                                 'payment_methods.name',
                                                 'payment_methods.processor_type',
                                                 DB::raw('COUNT(*) as failure_count')
                                             )
                                             ->groupBy('payment_methods.id', 'payment_methods.name', 'payment_methods.processor_type')
                                             ->orderByDesc('failure_count')
                                             ->get();

        return [
            'top_error_codes' => $failures->toArray(),
            'failures_by_gateway' => $failuresByGateway->toArray(),
        ];
    }

    /**
     * Get real-time metrics.
     *
     * @return array
     */
    public function getRealTimeMetrics(): array
    {
        $last24Hours = [Carbon::now()->subDay(), Carbon::now()];
        $lastHour = [Carbon::now()->subHour(), Carbon::now()];
        
        return [
            'transactions_last_hour' => PaymentTransaction::whereBetween('created_at', $lastHour)->count(),
            'successful_last_hour' => PaymentTransaction::whereBetween('created_at', $lastHour)
                                                       ->where('status', 'completed')
                                                       ->count(),
            'volume_last_hour' => PaymentTransaction::whereBetween('created_at', $lastHour)
                                                  ->where('status', 'completed')
                                                  ->sum('amount'),
            'transactions_last_24h' => PaymentTransaction::whereBetween('created_at', $last24Hours)->count(),
            'successful_last_24h' => PaymentTransaction::whereBetween('created_at', $last24Hours)
                                                      ->where('status', 'completed')
                                                      ->count(),
            'volume_last_24h' => PaymentTransaction::whereBetween('created_at', $last24Hours)
                                                 ->where('status', 'completed')
                                                 ->sum('amount'),
            'active_payment_methods' => PaymentMethod::where('status', 'active')->count(),
            'pending_transactions' => PaymentTransaction::where('status', 'pending')->count(),
        ];
    }

    /**
     * Get reconciliation report.
     *
     * @param string $date
     * @return array
     */
    public function getReconciliationReport(string $date): array
    {
        $startDate = Carbon::parse($date)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();
        
        $transactions = PaymentTransaction::whereBetween('created_at', [$startDate, $endDate])
                                        ->where('status', 'completed')
                                        ->with(['paymentMethod', 'currency'])
                                        ->get();
        
        $refunds = Refund::whereBetween('created_at', [$startDate, $endDate])
                        ->where('status', 'processed')
                        ->with(['transaction'])
                        ->get();
        
        $chargebacks = Chargeback::whereBetween('created_at', [$startDate, $endDate])
                                ->with(['transaction'])
                                ->get();
        
        $summary = [
            'date' => $date,
            'total_transactions' => $transactions->count(),
            'total_volume' => $transactions->sum('amount'),
            'total_refunds' => $refunds->sum('amount'),
            'total_chargebacks' => $chargebacks->sum('amount'),
            'net_volume' => $transactions->sum('amount') - $refunds->sum('amount') - $chargebacks->sum('amount'),
        ];
        
        $byGateway = $transactions->groupBy('paymentMethod.processor_type')->map(function ($gatewayTransactions, $gateway) {
            return [
                'gateway' => $gateway,
                'transaction_count' => $gatewayTransactions->count(),
                'total_volume' => $gatewayTransactions->sum('amount'),
                'avg_amount' => $gatewayTransactions->avg('amount'),
            ];
        })->values();
        
        return [
            'summary' => $summary,
            'by_gateway' => $byGateway->toArray(),
            'transactions' => $transactions->toArray(),
            'refunds' => $refunds->toArray(),
            'chargebacks' => $chargebacks->toArray(),
        ];
    }

    /**
     * Get alert conditions.
     *
     * @return array
     */
    public function checkAlertConditions(): array
    {
        $alerts = [];
        $last15Minutes = [Carbon::now()->subMinutes(15), Carbon::now()];
        
        // High failure rate alert
        $recentTransactions = PaymentTransaction::whereBetween('created_at', $last15Minutes)->count();
        $recentFailures = PaymentTransaction::whereBetween('created_at', $last15Minutes)
                                          ->where('status', 'failed')
                                          ->count();
        
        if ($recentTransactions > 10) {
            $failureRate = ($recentFailures / $recentTransactions) * 100;
            if ($failureRate > 20) {
                $alerts[] = [
                    'type' => 'high_failure_rate',
                    'severity' => 'critical',
                    'message' => "High failure rate detected: {$failureRate}% in the last 15 minutes",
                    'data' => ['failure_rate' => $failureRate, 'total_transactions' => $recentTransactions],
                ];
            }
        }
        
        // Slow processing time alert
        $avgProcessingTime = PaymentTransaction::whereBetween('created_at', $last15Minutes)
                                             ->where('status', 'completed')
                                             ->avg('processing_time_ms');
        
        if ($avgProcessingTime > 5000) { // 5 seconds
            $alerts[] = [
                'type' => 'slow_processing',
                'severity' => 'warning',
                'message' => "Slow processing time detected: {$avgProcessingTime}ms average in the last 15 minutes",
                'data' => ['avg_processing_time' => $avgProcessingTime],
            ];
        }
        
        // Inactive payment methods
        $inactiveMethods = PaymentMethod::where('status', 'inactive')->count();
        if ($inactiveMethods > 0) {
            $alerts[] = [
                'type' => 'inactive_methods',
                'severity' => 'info',
                'message' => "{$inactiveMethods} payment methods are currently inactive",
                'data' => ['inactive_count' => $inactiveMethods],
            ];
        }
        
        return $alerts;
    }

    /**
     * Get date range based on period.
     *
     * @param string $period
     * @return array
     */
    protected function getDateRange(string $period): array
    {
        switch ($period) {
            case '7d':
                return [Carbon::now()->subDays(7), Carbon::now()];
            case '30d':
                return [Carbon::now()->subDays(30), Carbon::now()];
            case '90d':
                return [Carbon::now()->subDays(90), Carbon::now()];
            case '1y':
                return [Carbon::now()->subYear(), Carbon::now()];
            default:
                return [Carbon::now()->subDays(30), Carbon::now()];
        }
    }

    /**
     * Get date format for grouping.
     *
     * @param string $groupBy
     * @return string
     */
    protected function getDateFormat(string $groupBy): string
    {
        switch ($groupBy) {
            case 'hour':
                return '%Y-%m-%d %H:00:00';
            case 'day':
                return '%Y-%m-%d';
            case 'week':
                return '%Y-%u';
            case 'month':
                return '%Y-%m';
            default:
                return '%Y-%m-%d';
        }
    }
}
