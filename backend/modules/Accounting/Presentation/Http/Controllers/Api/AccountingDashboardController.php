<?php

declare(strict_types=1);

namespace Modules\Accounting\Presentation\Http\Controllers\Api;

use App\Http\Controllers\ApiResponseEnvelope;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Accounting\Infrastructure\Persistence\ChartOfAccountRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\JournalEntryRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\FiscalYearRepositoryInterface;
use Modules\Accounting\Infrastructure\Persistence\BudgetRepositoryInterface;

class AccountingDashboardController extends Controller
{
    use ApiResponseEnvelope;

    public function __construct(
        protected ChartOfAccountRepositoryInterface $accountRepository,
        protected JournalEntryRepositoryInterface $journalRepository,
        protected FiscalYearRepositoryInterface $fiscalYearRepository,
        protected BudgetRepositoryInterface $budgetRepository,
    ) {}

    public function stats(): JsonResponse
    {
        $activeAccounts = $this->accountRepository->count(['is_active' => true]);
        $draftEntries = $this->journalRepository->count(['state' => 'draft']);
        $postedEntries = $this->journalRepository->count(['state' => 'posted']);
        $activeFiscalYears = $this->fiscalYearRepository->count(['is_active' => true]);
        $activeBudgets = $this->budgetRepository->count(['status' => 'active']);

        $totalDebit = $this->journalRepository->sumPosted('total_debit');
        $totalCredit = $this->journalRepository->sumPosted('total_credit');

        return $this->apiSuccess([
            'active_accounts' => $activeAccounts,
            'draft_entries' => $draftEntries,
            'posted_entries' => $postedEntries,
            'active_fiscal_years' => $activeFiscalYears,
            'active_budgets' => $activeBudgets,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ]);
    }

    public function recentEntries(): JsonResponse
    {
        $entries = $this->journalRepository->list(['limit' => 10]);

        return $this->apiSuccess($entries);
    }

    public function accountBalances(): JsonResponse
    {
        $accounts = $this->accountRepository->getActiveGroupedByType();

        return $this->apiSuccess($accounts);
    }
}
