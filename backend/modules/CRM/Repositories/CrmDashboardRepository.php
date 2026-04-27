<?php

namespace Modules\CRM\Repositories;

use Illuminate\Support\Facades\DB;

class CrmDashboardRepository implements CrmDashboardRepositoryInterface
{
    public function getDashboardData(): array
    {
        return [
            'contacts_count' => DB::table('crm_contacts')->count(),
            'companies_count' => DB::table('crm_companies')->count(),
            'deals_count' => DB::table('crm_opportunities')->count(),
            'recent_contacts' => DB::table('crm_contacts')->orderBy('created_at', 'desc')->limit(5)->get(),
            'pipeline_stages' => $this->getPipelineStages(),
            'leads_by_source' => $this->getLeadsBySource(),
            'monthly_leads' => $this->getMonthlyLeads(),
        ];
    }

    private function getPipelineStages(): array
    {
        return DB::table('crm_opportunities')
            ->select('stage', DB::raw('COUNT(*) as count'), DB::raw('COALESCE(SUM(expected_revenue), 0) as total_value'))
            ->groupBy('stage')
            ->get()
            ->map(fn ($row) => [
                'stage' => $row->stage,
                'count' => $row->count,
                'total_value' => (float) $row->total_value,
            ])
            ->toArray();
    }

    private function getLeadsBySource(): array
    {
        return DB::table('crm_leads')
            ->select('source', DB::raw('COUNT(*) as count'))
            ->groupBy('source')
            ->get()
            ->map(fn ($row) => [
                'source' => $row->source,
                'count' => $row->count,
            ])
            ->toArray();
    }

    private function getMonthlyLeads(int $months = 6): array
    {
        return DB::table('crm_leads')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'month' => $row->month,
                'count' => $row->count,
            ])
            ->toArray();
    }
}
