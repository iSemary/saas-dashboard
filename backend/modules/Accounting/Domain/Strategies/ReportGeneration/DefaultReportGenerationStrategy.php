<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Strategies\ReportGeneration;

use Modules\Accounting\Domain\Entities\ChartOfAccount;
use Modules\Accounting\Domain\Entities\JournalEntry;

class DefaultReportGenerationStrategy implements ReportGenerationStrategyInterface
{
    public function supports(string $type): bool
    {
        return in_array($type, ['trial_balance', 'profit_loss', 'balance_sheet', 'cash_flow']);
    }

    public function generate(string $type, array $params = []): array
    {
        return match($type) {
            'trial_balance'  => $this->trialBalance($params),
            'profit_loss'    => $this->profitAndLoss($params),
            'balance_sheet'  => $this->balanceSheet($params),
            'cash_flow'      => $this->cashFlow($params),
            default          => [],
        };
    }

    private function trialBalance(array $params): array
    {
        $accounts = ChartOfAccount::with('journalItems')->where('is_active', true)->get();
        $lines = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $debit  = (float) $account->journalItems->sum('debit');
            $credit = (float) $account->journalItems->sum('credit');
            $balance = $account->isDebitAccount()
                ? (float) $account->opening_balance + $debit - $credit
                : (float) $account->opening_balance + $credit - $debit;

            if ($balance != 0) {
                $lines[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit'  => $account->isDebitAccount() ? $balance : 0,
                    'credit' => $account->isCreditAccount() ? $balance : 0,
                ];
                $totalDebit  += $account->isDebitAccount() ? $balance : 0;
                $totalCredit += $account->isCreditAccount() ? $balance : 0;
            }
        }

        return ['lines' => $lines, 'total_debit' => $totalDebit, 'total_credit' => $totalCredit];
    }

    private function profitAndLoss(array $params): array
    {
        $query = ChartOfAccount::with('journalItems')
            ->whereIn('type', ['income', 'expense'])
            ->where('is_active', true);

        if (!empty($params['from_date']) && !empty($params['to_date'])) {
            $query->whereHas('journalItems.journalEntry', function ($q) use ($params) {
                $q->whereBetween('entry_date', [$params['from_date'], $params['to_date']]);
                $q->where('state', 'posted');
            });
        }

        $accounts = $query->get();
        $income = 0;
        $expenses = 0;
        $lines = [];

        foreach ($accounts as $account) {
            $debit  = (float) $account->journalItems->sum('debit');
            $credit = (float) $account->journalItems->sum('credit');
            $balance = $account->isDebitAccount()
                ? (float) $account->opening_balance + $debit - $credit
                : (float) $account->opening_balance + $credit - $debit;

            $lines[] = [
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'balance' => abs($balance),
            ];

            if ($account->type === 'income') {
                $income += abs($balance);
            } else {
                $expenses += abs($balance);
            }
        }

        return [
            'lines' => $lines,
            'total_income' => $income,
            'total_expenses' => $expenses,
            'net_profit' => $income - $expenses,
        ];
    }

    private function balanceSheet(array $params): array
    {
        $accounts = ChartOfAccount::with('journalItems')
            ->whereIn('type', ['asset', 'liability', 'equity'])
            ->where('is_active', true)
            ->get();

        $assets = [];
        $liabilities = [];
        $equity = [];
        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        foreach ($accounts as $account) {
            $debit  = (float) $account->journalItems->sum('debit');
            $credit = (float) $account->journalItems->sum('credit');
            $balance = $account->isDebitAccount()
                ? (float) $account->opening_balance + $debit - $credit
                : (float) $account->opening_balance + $credit - $debit;

            $line = ['code' => $account->code, 'name' => $account->name, 'balance' => abs($balance)];

            match($account->type) {
                'asset'     => ($assets[] = $line) && ($totalAssets += abs($balance)),
                'liability' => ($liabilities[] = $line) && ($totalLiabilities += abs($balance)),
                'equity'    => ($equity[] = $line) && ($totalEquity += abs($balance)),
                default     => null,
            };
        }

        return [
            'assets' => ['lines' => $assets, 'total' => $totalAssets],
            'liabilities' => ['lines' => $liabilities, 'total' => $totalLiabilities],
            'equity' => ['lines' => $equity, 'total' => $totalEquity],
            'total_liabilities_equity' => $totalLiabilities + $totalEquity,
        ];
    }

    private function cashFlow(array $params): array
    {
        return ['lines' => [], 'operating' => 0, 'investing' => 0, 'financing' => 0, 'net' => 0];
    }
}
