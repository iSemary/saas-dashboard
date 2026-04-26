<?php

declare(strict_types=1);

namespace Modules\Accounting\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Accounting\Domain\Events\JournalEntryPosted;
use Modules\Accounting\Domain\Events\JournalEntryCreated;
use Modules\Accounting\Domain\Events\FiscalYearCreated;
use Modules\Accounting\Domain\Events\BudgetCreated;

class EventServiceProvider extends ServiceProvider
{
    protected static $shouldDiscoverEvents = false;

    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        JournalEntryPosted::class => [
            // RecalculateAccountBalances::class,
        ],
        JournalEntryCreated::class => [
            //
        ],
        FiscalYearCreated::class => [
            //
        ],
        BudgetCreated::class => [
            //
        ],
    ];

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
        //
    }
}
