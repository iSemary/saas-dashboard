<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Exceptions;

use RuntimeException;

class ImportValidationException extends RuntimeException
{
    /**
     * @param array $errors Array of error messages per row
     */
    public function __construct(
        private array $errors,
        ?string $message = null
    ) {
        $count = count($errors);
        $defaultMessage = "Import validation failed with {$count} error(s)";

        parent::__construct($message ?? $defaultMessage);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorCount(): int
    {
        return count($this->errors);
    }
}
