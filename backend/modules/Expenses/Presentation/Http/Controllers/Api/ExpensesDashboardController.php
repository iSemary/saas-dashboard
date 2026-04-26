<?php
declare(strict_types=1);
namespace Modules\Expenses\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Expenses\Infrastructure\Persistence\ExpenseRepositoryInterface;
use Modules\Expenses\Infrastructure\Persistence\ExpenseReportRepositoryInterface;
use Modules\Expenses\Infrastructure\Persistence\ReimbursementRepositoryInterface;

class ExpensesDashboardController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected ExpenseRepositoryInterface $expenseRepository,
        protected ExpenseReportRepositoryInterface $reportRepository,
        protected ReimbursementRepositoryInterface $reimbursementRepository,
    ) {}

    public function stats(): JsonResponse
    {
        $userId = auth()->id();

        $totalExpenses = $this->expenseRepository->sumByCreator($userId, 'amount');
        $pendingCount = $this->expenseRepository->count(['created_by' => $userId, 'status' => 'pending']);
        $approvedCount = $this->expenseRepository->count(['created_by' => $userId, 'status' => 'approved']);
        $rejectedCount = $this->expenseRepository->count(['created_by' => $userId, 'status' => 'rejected']);
        $reimbursedTotal = $this->expenseRepository->sumByCreatorAndStatus($userId, 'reimbursed', 'amount');
        $pendingReports = $this->reportRepository->count(['created_by' => $userId, 'status' => 'submitted']);
        $pendingReimbursements = $this->reimbursementRepository->sumByCreatorAndStatus($userId, 'pending', 'amount');

        return $this->apiSuccess([
            'total_expenses' => $totalExpenses,
            'pending_count' => $pendingCount,
            'approved_count' => $approvedCount,
            'rejected_count' => $rejectedCount,
            'reimbursed_total' => $reimbursedTotal,
            'pending_reports' => $pendingReports,
            'pending_reimbursements' => $pendingReimbursements,
        ]);
    }

    public function recentExpenses(): JsonResponse
    {
        $expenses = $this->expenseRepository->list(['created_by' => auth()->id(), 'limit' => 10]);

        return $this->apiSuccess($expenses);
    }
}
