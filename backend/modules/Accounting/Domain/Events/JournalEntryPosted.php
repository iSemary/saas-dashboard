<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Accounting\Domain\Entities\JournalEntry;

class JournalEntryPosted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly JournalEntry $journalEntry,
        public readonly ?string $oldState = null,
        public readonly ?string $newState = null,
    ) {}
}
