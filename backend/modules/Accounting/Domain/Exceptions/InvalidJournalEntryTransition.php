<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Exceptions;

class InvalidJournalEntryTransition extends \RuntimeException
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition journal entry from '{$from}' to '{$to}'");
    }
}
