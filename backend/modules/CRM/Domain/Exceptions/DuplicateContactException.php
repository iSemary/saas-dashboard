<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Exceptions;

use RuntimeException;

class DuplicateContactException extends RuntimeException
{
    public function __construct(string $field, string $value, ?int $existingContactId = null)
    {
        $message = "A contact with {$field} '{$value}' already exists";
        if ($existingContactId) {
            $message .= " (Contact ID: {$existingContactId})";
        }

        parent::__construct($message);
    }
}
