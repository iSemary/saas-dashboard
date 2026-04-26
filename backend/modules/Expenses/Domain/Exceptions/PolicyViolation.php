<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Exceptions;

class PolicyViolation extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
