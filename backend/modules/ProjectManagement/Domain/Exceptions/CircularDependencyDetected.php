<?php

declare(strict_types=1);

namespace Modules\ProjectManagement\Domain\Exceptions;

use Exception;

class CircularDependencyDetected extends Exception
{
    public function __construct(string $taskId, string $dependencyId)
    {
        parent::__construct("Adding dependency [{$dependencyId}] to task [{$taskId}] would create a circular dependency.");
    }
}
