<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Exceptions;

class InvalidExpenseTransition extends \RuntimeException
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Cannot transition expense from '{$from}' to '{$to}'");
    }
}
