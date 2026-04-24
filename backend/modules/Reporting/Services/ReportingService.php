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

class ReportingService
{
    /**
     * Get CRM dashboard statistics.
     */
    public function getCrmStatistics(): array
    {
        return [
            'leads' => [
                'total' => Lead::count(),
                'new' => Lead::where('status', 'new')->count(),
                'qualified' => Lead::where('status', 'qualified')->count(),
                'converted' => Lead::where('status', 'converted')->count(),
                'this_month' => Lead::whereMonth('created_at', now()->month)->count(),
            ],
            'opportunities' => [
                'total' => Opportunity::count(),
                'open' => Opportunity::whereNotIn('stage', ['closed_won', 'closed_lost'])->count(),
                'won' => Opportunity::where('stage', 'closed_won')->count(),
                'lost' => Opportunity::where('stage', 'closed_lost')->count(),
                'pipeline_value' => Opportunity::whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('expected_revenue'),
            ],
            'conversion_rate' => $this->calculateConversionRate(),
        ];
    }

    /**
     * Get Sales dashboard statistics.
     */
    public function getSalesStatistics(): array
    {
        return [
            'products' => [
                'total' => Product::count(),
                'active' => Product::where('is_active', true)->count(),
                'low_stock' => Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count(),
                'out_of_stock' => Product::where('stock_quantity', 0)->count(),
            ],
            'orders' => [
                'total' => Order::count(),
                'pending' => Order::where('status', 'draft')->count(),
                'confirmed' => Order::where('status', 'confirmed')->count(),
                'delivered' => Order::where('status', 'delivered')->count(),
                'this_month' => Order::whereMonth('order_date', now()->month)->count(),
            ],
            'invoices' => [
                'total' => Invoice::count(),
                'paid' => Invoice::where('status', 'paid')->count(),
                'overdue' => Invoice::where('status', 'overdue')->count(),
                'pending' => Invoice::where('status', 'sent')->count(),
                'total_amount' => Invoice::sum('total_amount'),
                'paid_amount' => Invoice::sum('paid_amount'),
                'outstanding' => Invoice::sum('balance_amount'),
            ],
        ];
    }

    /**
     * Get lead conversion rate.
     */
    public function calculateConversionRate(): float
    {
        $totalLeads = Lead::count();
        $convertedLeads = Lead::where('status', 'converted')->count();

        return $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;
    }

    /**
     * Get sales pipeline data.
     */
    public function getSalesPipeline(): array
    {
        return Opportunity::select('stage', DB::raw('COUNT(*) as count'), DB::raw('SUM(expected_revenue) as total_value'))
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->groupBy('stage')
            ->get()
            ->toArray();
    }

    /**
     * Get monthly sales data.
     */
    public function getMonthlySalesData(int $months = 12): array
    {
        return Order::select(
                DB::raw('YEAR(order_date) as year'),
                DB::raw('MONTH(order_date) as month'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('order_date', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get top products by sales.
     */
    public function getTopProducts(int $limit = 10): array
    {
        // This would require a pivot table for order items
        // For now, return basic product data
        return Product::select('name', 'price', 'stock_quantity')
            ->where('is_active', true)
            ->orderBy('price', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get lead sources data.
     */
    public function getLeadSources(): array
    {
        return Lead::select('source', DB::raw('COUNT(*) as count'))
            ->groupBy('source')
            ->get()
            ->toArray();
    }

    /**
     * Get revenue by month.
     */
    public function getRevenueByMonth(int $months = 12): array
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
            ->toArray();
    }

    /**
     * Get HR dashboard statistics.
     */
    public function getHrStatistics(): array
    {
        return [
            'employees' => [
                'total' => Employee::count(),
                'active' => Employee::where('employment_status', 'active')->count(),
                'terminated' => Employee::where('employment_status', 'terminated')->count(),
                'new_this_month' => Employee::whereMonth('hire_date', now()->month)->count(),
            ],
            'attendance' => [
                'present_today' => Attendance::where('date', now()->toDateString())
                    ->where('status', 'present')->count(),
                'absent_today' => Attendance::where('date', now()->toDateString())
                    ->where('status', 'absent')->count(),
                'late_today' => Attendance::where('date', now()->toDateString())
                    ->where('status', 'late')->count(),
                'average_hours' => Attendance::where('date', '>=', now()->subDays(30))
                    ->avg('total_hours') ?? 0,
            ],
            'payroll' => [
                'total_paid' => Payroll::where('status', 'paid')->sum('net_pay'),
                'pending_approval' => Payroll::where('status', 'calculated')->count(),
                'this_month' => Payroll::whereMonth('pay_date', now()->month)
                    ->where('status', 'paid')->sum('net_pay'),
            ],
            'leave_requests' => [
                'pending' => LeaveRequest::where('status', 'pending')->count(),
                'approved_this_month' => LeaveRequest::whereMonth('start_date', now()->month)
                    ->where('status', 'approved')->count(),
                'emergency' => LeaveRequest::where('is_emergency', true)
                    ->where('status', 'pending')->count(),
            ],
        ];
    }

    /**
     * Get Inventory dashboard statistics.
     */
    public function getInventoryStatistics(): array
    {
        return [
            'warehouses' => [
                'total' => Warehouse::count(),
                'active' => Warehouse::where('is_active', true)->count(),
            ],
            'stock_moves' => [
                'total_moves' => StockMove::count(),
                'incoming_today' => StockMove::where('date', now()->toDateString())
                    ->where('move_type', 'in')->count(),
                'outgoing_today' => StockMove::where('date', now()->toDateString())
                    ->where('move_type', 'out')->count(),
                'pending_validation' => StockMove::where('state', 'draft')->count(),
            ],
            'products' => [
                'low_stock' => Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->count(),
                'out_of_stock' => Product::where('stock_quantity', 0)->count(),
                'total_value' => Product::sum(DB::raw('stock_quantity * cost')),
            ],
        ];
    }

    /**
     * Get Accounting dashboard statistics.
     */
    public function getAccountingStatistics(): array
    {
        return [
            'journal_entries' => [
                'total' => JournalEntry::count(),
                'posted' => JournalEntry::where('state', 'posted')->count(),
                'draft' => JournalEntry::where('state', 'draft')->count(),
                'this_month' => JournalEntry::whereMonth('entry_date', now()->month)->count(),
            ],
            'accounts' => [
                'total' => ChartOfAccount::count(),
                'active' => ChartOfAccount::where('is_active', true)->count(),
                'leaf_accounts' => ChartOfAccount::where('is_leaf', true)->count(),
            ],
            'balances' => [
                'total_debit' => JournalEntry::where('state', 'posted')->sum('total_debit'),
                'total_credit' => JournalEntry::where('state', 'posted')->sum('total_credit'),
            ],
        ];
    }

    /**
     * Get comprehensive analytics data.
     */
    public function getComprehensiveAnalytics(): array
    {
        return [
            'overview' => [
                'total_revenue' => $this->getTotalRevenue(),
                'total_customers' => $this->getTotalCustomers(),
                'total_employees' => Employee::where('employment_status', 'active')->count(),
                'total_products' => Product::where('is_active', true)->count(),
            ],
            'trends' => [
                'revenue_growth' => $this->getRevenueGrowth(),
                'customer_growth' => $this->getCustomerGrowth(),
                'employee_growth' => $this->getEmployeeGrowth(),
            ],
            'performance' => [
                'conversion_rate' => $this->calculateConversionRate(),
                'average_deal_size' => $this->getAverageDealSize(),
                'customer_retention' => $this->getCustomerRetention(),
            ],
        ];
    }

    /**
     * Get total revenue.
     */
    public function getTotalRevenue(): float
    {
        return Invoice::where('status', 'paid')->sum('total_amount') ?? 0;
    }

    /**
     * Get total customers.
     */
    public function getTotalCustomers(): int
    {
        return DB::table('contacts')->distinct('email')->count();
    }

    /**
     * Get revenue growth percentage.
     */
    public function getRevenueGrowth(): float
    {
        $currentMonth = Invoice::where('status', 'paid')
            ->whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->sum('total_amount') ?? 0;

        $lastMonth = Invoice::where('status', 'paid')
            ->whereMonth('invoice_date', now()->subMonth()->month)
            ->whereYear('invoice_date', now()->subMonth()->year)
            ->sum('total_amount') ?? 0;

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return (($currentMonth - $lastMonth) / $lastMonth) * 100;
    }

    /**
     * Get customer growth percentage.
     */
    public function getCustomerGrowth(): float
    {
        $currentMonth = DB::table('contacts')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonth = DB::table('contacts')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return (($currentMonth - $lastMonth) / $lastMonth) * 100;
    }

    /**
     * Get employee growth percentage.
     */
    public function getEmployeeGrowth(): float
    {
        $currentMonth = Employee::whereMonth('hire_date', now()->month)
            ->whereYear('hire_date', now()->year)
            ->count();

        $lastMonth = Employee::whereMonth('hire_date', now()->subMonth()->month)
            ->whereYear('hire_date', now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return (($currentMonth - $lastMonth) / $lastMonth) * 100;
    }

    /**
     * Get average deal size.
     */
    public function getAverageDealSize(): float
    {
        return Opportunity::where('stage', 'closed_won')->avg('expected_revenue') ?? 0;
    }

    /**
     * Get customer retention rate.
     */
    public function getCustomerRetention(): float
    {
        // This is a simplified calculation
        // In a real scenario, you'd track customer repeat purchases
        $totalCustomers = $this->getTotalCustomers();
        $repeatCustomers = DB::table('orders')
            ->select('contact_id')
            ->groupBy('contact_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        return $totalCustomers > 0 ? ($repeatCustomers / $totalCustomers) * 100 : 0;
    }

    /**
     * Get department-wise employee distribution.
     */
    public function getDepartmentDistribution(): array
    {
        return Employee::select('department', DB::raw('COUNT(*) as count'))
            ->where('employment_status', 'active')
            ->groupBy('department')
            ->get()
            ->toArray();
    }

    /**
     * Get attendance trends.
     */
    public function getAttendanceTrends(int $days = 30): array
    {
        return Attendance::select(
                DB::raw('DATE(date) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late')
            )
            ->where('date', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get payroll trends.
     */
    public function getPayrollTrends(int $months = 12): array
    {
        return Payroll::select(
                DB::raw('YEAR(pay_date) as year'),
                DB::raw('MONTH(pay_date) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(net_pay) as total_pay'),
                DB::raw('AVG(net_pay) as average_pay')
            )
            ->where('status', 'paid')
            ->where('pay_date', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get leave request trends.
     */
    public function getLeaveRequestTrends(int $months = 12): array
    {
        return LeaveRequest::select(
                DB::raw('YEAR(start_date) as year'),
                DB::raw('MONTH(start_date) as month'),
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                DB::raw('SUM(total_days) as total_days')
            )
            ->where('start_date', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get stock movement trends.
     */
    public function getStockMovementTrends(int $days = 30): array
    {
        return StockMove::select(
                DB::raw('DATE(date) as date'),
                DB::raw('COUNT(*) as total_moves'),
                DB::raw('SUM(CASE WHEN move_type = "in" THEN quantity ELSE 0 END) as incoming'),
                DB::raw('SUM(CASE WHEN move_type = "out" THEN quantity ELSE 0 END) as outgoing'),
                DB::raw('SUM(total_cost) as total_value')
            )
            ->where('date', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get financial trends.
     */
    public function getFinancialTrends(int $months = 12): array
    {
        return JournalEntry::select(
                DB::raw('YEAR(entry_date) as year'),
                DB::raw('MONTH(entry_date) as month'),
                DB::raw('COUNT(*) as entries'),
                DB::raw('SUM(total_debit) as total_debit'),
                DB::raw('SUM(total_credit) as total_credit')
            )
            ->where('state', 'posted')
            ->where('entry_date', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get dashboard widgets data.
     */
    public function getDashboardWidgets(): array
    {
        return [
            'crm_stats' => $this->getCrmStatistics(),
            'sales_stats' => $this->getSalesStatistics(),
            'hr_stats' => $this->getHrStatistics(),
            'inventory_stats' => $this->getInventoryStatistics(),
            'accounting_stats' => $this->getAccountingStatistics(),
            'analytics' => $this->getComprehensiveAnalytics(),
            'pipeline' => $this->getSalesPipeline(),
            'monthly_sales' => $this->getMonthlySalesData(6),
            'lead_sources' => $this->getLeadSources(),
            'revenue_trend' => $this->getRevenueByMonth(6),
            'department_distribution' => $this->getDepartmentDistribution(),
            'attendance_trends' => $this->getAttendanceTrends(30),
            'payroll_trends' => $this->getPayrollTrends(12),
            'leave_trends' => $this->getLeaveRequestTrends(12),
            'stock_trends' => $this->getStockMovementTrends(30),
            'financial_trends' => $this->getFinancialTrends(12),
        ];
    }

    /**
     * Generate custom report.
     */
    public function generateCustomReport(array $config): array
    {
        $module = $config['module'] ?? 'crm';
        $type = $config['type'] ?? 'summary';
        $filters = $config['filters'] ?? [];
        $dateRange = $config['date_range'] ?? ['start' => now()->subMonth(), 'end' => now()];

        switch ($module) {
            case 'crm':
                return $this->generateCrmReport($type, $filters, $dateRange);
            case 'sales':
                return $this->generateSalesReport($type, $filters, $dateRange);
            case 'hr':
                return $this->generateHrReport($type, $filters, $dateRange);
            case 'inventory':
                return $this->generateInventoryReport($type, $filters, $dateRange);
            case 'accounting':
                return $this->generateAccountingReport($type, $filters, $dateRange);
            default:
                return ['error' => 'Invalid module specified'];
        }
    }

    /**
     * Generate CRM report.
     */
    private function generateCrmReport(string $type, array $filters, array $dateRange): array
    {
        $query = Lead::query();
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['source'])) {
            $query->where('source', $filters['source']);
        }
        
        $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

        switch ($type) {
            case 'summary':
                return [
                    'total_leads' => $query->count(),
                    'converted_leads' => $query->where('status', 'converted')->count(),
                    'conversion_rate' => $this->calculateConversionRate(),
                    'top_sources' => $this->getLeadSources(),
                ];
            case 'detailed':
                return $query->with(['assignedUser', 'activities'])->get()->toArray();
            default:
                return ['error' => 'Invalid report type'];
        }
    }

    /**
     * Generate Sales report.
     */
    private function generateSalesReport(string $type, array $filters, array $dateRange): array
    {
        $query = Order::query();
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        $query->whereBetween('order_date', [$dateRange['start'], $dateRange['end']]);

        switch ($type) {
            case 'summary':
                return [
                    'total_orders' => $query->count(),
                    'total_revenue' => $query->sum('total_amount'),
                    'average_order_value' => $query->avg('total_amount'),
                    'top_products' => $this->getTopProducts(10),
                ];
            case 'detailed':
                return $query->with(['contact', 'company'])->get()->toArray();
            default:
                return ['error' => 'Invalid report type'];
        }
    }

    /**
     * Generate HR report.
     */
    private function generateHrReport(string $type, array $filters, array $dateRange): array
    {
        switch ($type) {
            case 'attendance':
                return $this->getAttendanceTrends(30);
            case 'payroll':
                return $this->getPayrollTrends(12);
            case 'leave':
                return $this->getLeaveRequestTrends(12);
            case 'summary':
                return $this->getHrStatistics();
            default:
                return ['error' => 'Invalid report type'];
        }
    }

    /**
     * Generate Inventory report.
     */
    private function generateInventoryReport(string $type, array $filters, array $dateRange): array
    {
        switch ($type) {
            case 'movement':
                return $this->getStockMovementTrends(30);
            case 'summary':
                return $this->getInventoryStatistics();
            default:
                return ['error' => 'Invalid report type'];
        }
    }

    /**
     * Generate Accounting report.
     */
    private function generateAccountingReport(string $type, array $filters, array $dateRange): array
    {
        switch ($type) {
            case 'financial':
                return $this->getFinancialTrends(12);
            case 'summary':
                return $this->getAccountingStatistics();
            default:
                return ['error' => 'Invalid report type'];
        }
    }
}
