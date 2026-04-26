<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Strategies\JournalValidation;

use Modules\Accounting\Domain\Entities\JournalEntry;

interface JournalValidationStrategyInterface
{
    public function validate(JournalEntry $entry): void;
}
