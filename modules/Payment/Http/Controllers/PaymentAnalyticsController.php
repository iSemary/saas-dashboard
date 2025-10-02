<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\ApiController;
use Modules\Payment\Services\PaymentAnalyticsService;
use Modules\Payment\Services\PaymentMonitoringService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PaymentAnalyticsController extends ApiController implements HasMiddleware
{
    protected PaymentAnalyticsService $analyticsService;
    protected PaymentMonitoringService $monitoringService;

    public function __construct(
        PaymentAnalyticsService $analyticsService,
        PaymentMonitoringService $monitoringService
    ) {
        $this->analyticsService = $analyticsService;
        $this->monitoringService = $monitoringService;
    }

    public function index()
    {
        $title = translate('payment_analytics');
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => translate('payment_analytics')],
        ];

        return view('landlord.payment.analytics.index', compact('breadcrumbs', 'title'));
    }

    public function overview(Request $request)
    {
        $period = $request->get('period', '30d');
        $overview = $this->analyticsService->getPaymentOverview($period);
        
        return $this->return(200, 'Overview retrieved successfully', $overview);
    }

    public function paymentMethodPerformance(Request $request)
    {
        $period = $request->get('period', '30d');
        $performance = $this->analyticsService->getPaymentMethodPerformance($period);
        
        return $this->return(200, 'Payment method performance retrieved successfully', $performance);
    }

    public function transactionTrends(Request $request)
    {
        $period = $request->get('period', '30d');
        $groupBy = $request->get('group_by', 'day');
        $trends = $this->analyticsService->getTransactionTrends($period, $groupBy);
        
        return $this->return(200, 'Transaction trends retrieved successfully', $trends);
    }

    public function currencyDistribution(Request $request)
    {
        $period = $request->get('period', '30d');
        $distribution = $this->analyticsService->getCurrencyDistribution($period);
        
        return $this->return(200, 'Currency distribution retrieved successfully', $distribution);
    }

    public function failureAnalysis(Request $request)
    {
        $period = $request->get('period', '30d');
        $analysis = $this->analyticsService->getFailureAnalysis($period);
        
        return $this->return(200, 'Failure analysis retrieved successfully', $analysis);
    }

    public function realTimeMetrics()
    {
        $metrics = $this->analyticsService->getRealTimeMetrics();
        
        return $this->return(200, 'Real-time metrics retrieved successfully', $metrics);
    }

    public function reconciliationReport(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->get('date');
        $report = $this->analyticsService->getReconciliationReport($date);
        
        return $this->return(200, 'Reconciliation report generated successfully', $report);
    }

    public function systemHealth()
    {
        $health = $this->monitoringService->getSystemHealth();
        
        return $this->return(200, 'System health retrieved successfully', $health);
    }

    public function performanceMetrics()
    {
        $metrics = $this->monitoringService->getPerformanceMetrics();
        
        return $this->return(200, 'Performance metrics retrieved successfully', $metrics);
    }

    public function exportReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:overview,performance,trends,reconciliation',
            'period' => 'nullable|in:7d,30d,90d,1y',
            'date' => 'nullable|date_format:Y-m-d',
            'format' => 'nullable|in:csv,xlsx,pdf',
        ]);

        $type = $request->get('type');
        $period = $request->get('period', '30d');
        $format = $request->get('format', 'csv');

        try {
            switch ($type) {
                case 'overview':
                    $data = $this->analyticsService->getPaymentOverview($period);
                    break;
                case 'performance':
                    $data = $this->analyticsService->getPaymentMethodPerformance($period);
                    break;
                case 'trends':
                    $data = $this->analyticsService->getTransactionTrends($period);
                    break;
                case 'reconciliation':
                    $date = $request->get('date', now()->format('Y-m-d'));
                    $data = $this->analyticsService->getReconciliationReport($date);
                    break;
                default:
                    return $this->return(400, 'Invalid report type');
            }

            // Generate export file
            $filename = "payment_{$type}_" . now()->format('Y-m-d_H-i-s') . ".{$format}";
            
            // TODO: Implement actual file generation based on format
            // For now, return the data
            return $this->return(200, 'Report exported successfully', [
                'filename' => $filename,
                'data' => $data,
                'format' => $format,
            ]);

        } catch (\Exception $e) {
            return $this->return(500, 'Export failed: ' . $e->getMessage());
        }
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.payment_analytics', only: ['index', 'overview', 'paymentMethodPerformance', 'transactionTrends', 'currencyDistribution', 'failureAnalysis', 'realTimeMetrics', 'reconciliationReport', 'systemHealth', 'performanceMetrics']),
            new Middleware('permission:export.payment_analytics', only: ['exportReport']),
        ];
    }
}
