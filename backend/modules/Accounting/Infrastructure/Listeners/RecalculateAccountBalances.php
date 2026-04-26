<?php

declare(strict_types=1);

namespace Modules\Accounting\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Domain\Strategies\BalanceCalculation\BalanceCalculationStrategyInterface;
use Modules\Accounting\Domain\Entities\ChartOfAccount;
use Modules\Accounting\Domain\Events\JournalEntryPosted;

class RecalculateAccountBalances implements ShouldQueue
{
    public function __construct(
        private readonly BalanceCalculationStrategyInterface $balanceStrategy,
    ) {}

    public function handle(JournalEntryPosted $event): void
    {
        $entry = $event->journalEntry;

        foreach ($entry->journalItems as $item) {
            try {
                $account = ChartOfAccount::findOrFail($item->account_id);
                $newBalance = $this->balanceStrategy->calculate($account);
                $account->update(['current_balance' => $newBalance]);
            } catch (\Throwable $e) {
                Log::error('Failed to recalculate account balance', [
                    'account_id' => $item->account_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
