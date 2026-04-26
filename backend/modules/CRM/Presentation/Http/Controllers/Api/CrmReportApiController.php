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
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
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
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
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
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }

    public function leadsBySource(): JsonResponse
    {
        try {
            return $this->apiSuccess($this->leads->getCountByStatus());
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
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
                    'leads' => $this->leads->countByMonth($date->year, $date->month),
                    'opportunities' => $this->opportunities->countByMonth($date->year, $date->month),
                    'closed_won' => $this->opportunities->countClosedWonByMonth($date->year, $date->month),
                ];
            }

            return $this->apiSuccess($trends);
        } catch (\Throwable $e) {
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
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
            return $this->apiError(translate('message.operation_failed'), 500, $e->getMessage());
        }
    }
}
