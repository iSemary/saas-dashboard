<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Exceptions;

use Exception;

class WipLimitExceeded extends Exception
{
    public function __construct(string $columnName, int $limit)
    {
        parent::__construct("WIP limit of {$limit} exceeded in column [{$columnName}].");
    }
}
