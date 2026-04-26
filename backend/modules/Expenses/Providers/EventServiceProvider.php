<?php
declare(strict_types=1);
namespace Modules\Expenses\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Expenses\Domain\Events\ExpenseSubmitted;
use Modules\Expenses\Domain\Events\ExpenseApproved;
use Modules\Expenses\Domain\Events\ExpenseRejected;
use Modules\Expenses\Domain\Events\ExpenseReimbursed;
use Modules\Expenses\Domain\Events\ReportSubmitted;
use Modules\Expenses\Domain\Events\ReportApproved;
use Modules\Expenses\Domain\Events\ReportRejected;
use Modules\Expenses\Infrastructure\Listeners\CreateJournalEntryOnExpenseApproved;
use Modules\Expenses\Infrastructure\Listeners\CreateJournalEntryOnReimbursement;

class EventServiceProvider extends ServiceProvider
{
    protected static $shouldDiscoverEvents = false;

    protected $listen = [
        ExpenseApproved::class => [
            CreateJournalEntryOnExpenseApproved::class,
        ],
        ExpenseReimbursed::class => [
            CreateJournalEntryOnReimbursement::class,
        ],
    ];
}
