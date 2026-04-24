<?php

namespace Modules\Reporting\Services;

use Modules\CRM\Models\Lead;
use Modules\CRM\Models\Opportunity;
use Modules\Sales\Models\Product;
use Modules\Sales\Models\Order;
use Modules\Sales\Models\Invoice;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Attendance;
use Modules\HR\Models\Payroll;
use Modules\HR\Models\LeaveRequest;
use Modules\Inventory\Models\StockMove;
use Modules\Inventory\Models\Warehouse;
use Modules\Accounting\Models\JournalEntry;
use Modules\Accounting\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get KPI metrics for dashboard.
     */
    public function getKpiMetrics(): array
    {
        return [
            'revenue' => [
                'current' => $this->getCurrentMonthRevenue(),
                'previous' => $this->getPreviousMonthRevenue(),
                'growth' => $this->getRevenueGrowthPercentage(),
                'target' => $this->getRevenueTarget(),
            ],
            'customers' => [
                'current' => $this->getCurrentMonthCustomers(),
                'previous' => $this->getPreviousMonthCustomers(),
                'growth' => $this->getCustomerGrowthPercentage(),
                'target' => $this->getCustomerTarget(),
            ],
            'leads' => [
                'current' => $this->getCurrentMonthLeads(),
                'previous' => $this->getPreviousMonthLeads(),
                'growth' => $this->getLeadGrowthPercentage(),
                'conversion_rate' => $this->getLeadConversionRate(),
            ],
            'employees' => [
                'current' => $this->getCurrentMonthEmployees(),
                'previous' => $this->getPreviousMonthEmployees(),
                'growth' => $this->getEmployeeGrowthPercentage(),
                'attendance_rate' => $this->getAttendanceRate(),
            ],
        ];
    }

    /**
     * Get performance trends.
     */
    public function getPerformanceTrends(int $months = 12): array
    {
        return [
            'revenue_trend' => $this->getRevenueTrend($months),
            'customer_trend' => $this->getCustomerTrend($months),
            'lead_trend' => $this->getLeadTrend($months),
            'employee_trend' => $this->getEmployeeTrend($months),
            'conversion_trend' => $this->getConversionTrend($months),
        ];
    }

    /**
     * Get comprehensive analytics data.
     */
    public function getComprehensiveAnalytics(): array
    {
        return [
            'kpi_metrics' => $this->getKpiMetrics(),
            'performance_trends' => $this->getPerformanceTrends(),
            'data_visualization' => $this->getDataVisualizationData(),
            'predictive_analytics' => $this->getPredictiveAnalytics(),
        ];
    }

    // Private helper methods for KPI calculations

    private function getCurrentMonthRevenue(): float
    {
        return Invoice::where('status', 'paid')
            ->whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->sum('total_amount') ?? 0;
    }

    private function getPreviousMonthRevenue(): float
    {
        return Invoice::where('status', 'paid')
            ->whereMonth('invoice_date', now()->subMonth()->month)
            ->whereYear('invoice_date', now()->subMonth()->year)
            ->sum('total_amount') ?? 0;
    }

    private function getRevenueGrowthPercentage(): float
    {
        $current = $this->getCurrentMonthRevenue();
        $previous = $this->getPreviousMonthRevenue();
        
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return (($current - $previous) / $previous) * 100;
    }

    private function getRevenueTarget(): float
    {
        return 100000; // Example target
    }

    private function getCurrentMonthCustomers(): int
    {
        return DB::table('contacts')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function getPreviousMonthCustomers(): int
    {
        return DB::table('contacts')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
    }

    private function getCustomerGrowthPercentage(): float
    {
        $current = $this->getCurrentMonthCustomers();
        $previous = $this->getPreviousMonthCustomers();
        
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return (($current - $previous) / $previous) * 100;
    }

    private function getCustomerTarget(): int
    {
        return 50; // Example target
    }

    private function getCurrentMonthLeads(): int
    {
        return Lead::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function getPreviousMonthLeads(): int
    {
        return Lead::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
    }

    private function getLeadGrowthPercentage(): float
    {
        $current = $this->getCurrentMonthLeads();
        $previous = $this->getPreviousMonthLeads();
        
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return (($current - $previous) / $previous) * 100;
    }

    private function getLeadConversionRate(): float
    {
        $totalLeads = Lead::count();
        $convertedLeads = Lead::where('status', 'converted')->count();
        
        return $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;
    }

    private function getCurrentMonthEmployees(): int
    {
        return Employee::whereMonth('hire_date', now()->month)
            ->whereYear('hire_date', now()->year)
            ->count();
    }

    private function getPreviousMonthEmployees(): int
    {
        return Employee::whereMonth('hire_date', now()->subMonth()->month)
            ->whereYear('hire_date', now()->subMonth()->year)
            ->count();
    }

    private function getEmployeeGrowthPercentage(): float
    {
        $current = $this->getCurrentMonthEmployees();
        $previous = $this->getPreviousMonthEmployees();
        
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return (($current - $previous) / $previous) * 100;
    }

    private function getAttendanceRate(): float
    {
        $totalDays = Attendance::where('date', '>=', now()->subDays(30))->count();
        $presentDays = Attendance::where('date', '>=', now()->subDays(30))
            ->where('status', 'present')
            ->count();
        
        return $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;
    }

    // Trend analysis methods

    private function getRevenueTrend(int $months): array
    {
        return Invoice::select(
                DB::raw('YEAR(invoice_date) as year'),
                DB::raw('MONTH(invoice_date) as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('status', 'paid')
            ->where('invoice_date', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'revenue' => $item->revenue,
                ];
            })
            ->toArray();
    }

    private function getCustomerTrend(int $months): array
    {
        return DB::table('contacts')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as customers')
            )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'customers' => $item->customers,
                ];
            })
            ->toArray();
    }

    private function getLeadTrend(int $months): array
    {
        return Lead::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as leads')
            )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'leads' => $item->leads,
                ];
            })
            ->toArray();
    }

    private function getEmployeeTrend(int $months): array
    {
        return Employee::select(
                DB::raw('YEAR(hire_date) as year'),
                DB::raw('MONTH(hire_date) as month'),
                DB::raw('COUNT(*) as employees')
            )
            ->where('hire_date', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'employees' => $item->employees,
                ];
            })
            ->toArray();
    }

    private function getConversionTrend(int $months): array
    {
        return Lead::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_leads'),
                DB::raw('SUM(CASE WHEN status = "converted" THEN 1 ELSE 0 END) as converted_leads')
            )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                $conversionRate = $item->total_leads > 0 ? ($item->converted_leads / $item->total_leads) * 100 : 0;
                return [
                    'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'conversion_rate' => round($conversionRate, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get data visualization data.
     */
    public function getDataVisualizationData(): array
    {
        return [
            'charts' => [
                'revenue_chart' => $this->getRevenueTrend(12),
                'customer_chart' => $this->getCustomerTrend(12),
                'pipeline_chart' => $this->getPipelineChartData(),
                'attendance_chart' => $this->getAttendanceChartData(),
            ],
            'graphs' => [
                'sales_funnel' => $this->getSalesFunnelData(),
                'employee_performance' => $this->getEmployeePerformanceData(),
            ],
        ];
    }

    private function getPipelineChartData(): array
    {
        return Opportunity::select('stage', DB::raw('COUNT(*) as count'), DB::raw('SUM(expected_revenue) as value'))
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->groupBy('stage')
            ->get()
            ->toArray();
    }

    private function getAttendanceChartData(): array
    {
        return Attendance::select(
                DB::raw('DATE(date) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present')
            )
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
    }

    private function getSalesFunnelData(): array
    {
        return [
            'leads' => Lead::count(),
            'qualified' => Lead::where('status', 'qualified')->count(),
            'opportunities' => Opportunity::count(),
            'won' => Opportunity::where('stage', 'closed_won')->count(),
        ];
    }

    private function getEmployeePerformanceData(): array
    {
        return Employee::select('department', DB::raw('COUNT(*) as count'))
            ->where('employment_status', 'active')
            ->groupBy('department')
            ->get()
            ->toArray();
    }

    /**
     * Get predictive analytics.
     */
    public function getPredictiveAnalytics(): array
    {
        return [
            'revenue_forecast' => $this->getRevenueForecast(),
            'customer_forecast' => $this->getCustomerForecast(),
            'lead_forecast' => $this->getLeadForecast(),
        ];
    }

    private function getRevenueForecast(): array
    {
        $historicalData = $this->getRevenueTrend(12);
        
        if (count($historicalData) < 2) {
            return ['forecast' => [], 'confidence' => 0];
        }
        
        $forecast = [];
        $lastValue = end($historicalData)['revenue'];
        $trend = $this->calculateTrend($historicalData);
        
        for ($i = 1; $i <= 3; $i++) {
            $forecast[] = [
                'period' => now()->addMonths($i)->format('Y-m'),
                'revenue' => max(0, $lastValue + ($trend * $i)),
            ];
        }
        
        return [
            'forecast' => $forecast,
            'confidence' => $this->calculateConfidence($historicalData),
        ];
    }

    private function getCustomerForecast(): array
    {
        $historicalData = $this->getCustomerTrend(12);
        
        if (count($historicalData) < 2) {
            return ['forecast' => [], 'confidence' => 0];
        }
        
        $forecast = [];
        $lastValue = end($historicalData)['customers'];
        $trend = $this->calculateTrend($historicalData);
        
        for ($i = 1; $i <= 3; $i++) {
            $forecast[] = [
                'period' => now()->addMonths($i)->format('Y-m'),
                'customers' => max(0, $lastValue + ($trend * $i)),
            ];
        }
        
        return [
            'forecast' => $forecast,
            'confidence' => $this->calculateConfidence($historicalData),
        ];
    }

    private function getLeadForecast(): array
    {
        $historicalData = $this->getLeadTrend(12);
        
        if (count($historicalData) < 2) {
            return ['forecast' => [], 'confidence' => 0];
        }
        
        $forecast = [];
        $lastValue = end($historicalData)['leads'];
        $trend = $this->calculateTrend($historicalData);
        
        for ($i = 1; $i <= 3; $i++) {
            $forecast[] = [
                'period' => now()->addMonths($i)->format('Y-m'),
                'leads' => max(0, $lastValue + ($trend * $i)),
            ];
        }
        
        return [
            'forecast' => $forecast,
            'confidence' => $this->calculateConfidence($historicalData),
        ];
    }

    // Helper methods

    private function calculateTrend(array $data): float
    {
        if (count($data) < 2) {
            return 0;
        }
        
        $first = reset($data);
        $last = end($data);
        
        $firstValue = is_array($first) ? $first['revenue'] ?? $first['customers'] ?? $first['leads'] ?? 0 : $first;
        $lastValue = is_array($last) ? $last['revenue'] ?? $last['customers'] ?? $last['leads'] ?? 0 : $last;
        
        return ($lastValue - $firstValue) / count($data);
    }

    private function calculateConfidence(array $data): float
    {
        if (count($data) < 3) {
            return 0;
        }
        
        $values = array_map(function ($item) {
            return is_array($item) ? $item['revenue'] ?? $item['customers'] ?? $item['leads'] ?? 0 : $item;
        }, $data);
        
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function ($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $values)) / count($values);
        
        $stdDev = sqrt($variance);
        $coefficient = $mean > 0 ? $stdDev / $mean : 0;
        
        return max(0, min(100, 100 - ($coefficient * 100)));
    }
}
