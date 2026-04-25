<?php

declare(strict_types=1);

namespace Modules\CRM\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CRM\Infrastructure\Persistence\LeadRepositoryInterface;
use Modules\CRM\Infrastructure\Persistence\OpportunityRepositoryInterface;
use Modules\CRM\Infrastructure\Persistence\ActivityRepositoryInterface;

class CrmReportApiController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        private readonly LeadRepositoryInterface $leads,
        private readonly OpportunityRepositoryInterface $opportunities,
        private readonly ActivityRepositoryInterface $activities,
    ) {}

    public function pipeline(Request $request): JsonResponse
    {
        try {
            return $this->apiSuccess($this->opportunities->getPipelineData());
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get pipeline report', 500, $e->getMessage());
        }
    }

    public function conversion(Request $request): JsonResponse
    {
        try {
            $data = [
                'lead_conversion_rate' => $this->leads->getConversionRate(),
                'opportunity_win_rate' => $this->opportunities->getStatistics()['win_rate'],
                'leads_by_status' => $this->leads->getCountByStatus(),
            ];
            return $this->apiSuccess($data);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get conversion report', 500, $e->getMessage());
        }
    }

    public function activity(Request $request): JsonResponse
    {
        try {
            $overdue = $this->activities->getOverdue();
            $upcoming = $this->activities->getUpcoming(7);
            $today = $this->activities->getForToday();
            return $this->apiSuccess([
                'overdue_count' => $overdue->count(),
                'upcoming_count' => $upcoming->count(),
                'today_count' => $today->count(),
                'overdue' => $overdue,
                'upcoming' => $upcoming,
                'today' => $today,
            ]);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get activity report', 500, $e->getMessage());
        }
    }

    public function leadsBySource(): JsonResponse
    {
        try {
            return $this->apiSuccess($this->leads->getCountByStatus());
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get leads by source report', 500, $e->getMessage());
        }
    }

    public function monthlyTrends(Request $request): JsonResponse
    {
        try {
            $months = (int) $request->get('months', 12);
            $trends = [];

            for ($i = $months - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $trends[] = [
                    'month' => $date->format('Y-m'),
                    'label' => $date->format('M Y'),
                    'leads' => \Modules\CRM\Domain\Entities\Lead::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)->count(),
                    'opportunities' => \Modules\CRM\Domain\Entities\Opportunity::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)->count(),
                    'closed_won' => \Modules\CRM\Domain\Entities\Opportunity::where('stage', 'closed_won')
                        ->whereYear('actual_close_date', $date->year)
                        ->whereMonth('actual_close_date', $date->month)->count(),
                ];
            }

            return $this->apiSuccess($trends);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get monthly trends', 500, $e->getMessage());
        }
    }

    public function overview(Request $request): JsonResponse
    {
        try {
            return $this->apiSuccess([
                'leads' => $this->leads->getStatistics(),
                'opportunities' => $this->opportunities->getStatistics(),
                'pipeline' => $this->opportunities->getPipelineData(),
            ]);
        } catch (\Throwable $e) {
            return $this->apiError('Failed to get overview report', 500, $e->getMessage());
        }
    }
}
