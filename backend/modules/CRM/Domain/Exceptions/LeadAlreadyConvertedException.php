<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Exceptions;

use RuntimeException;

class LeadAlreadyConvertedException extends RuntimeException
{
    public function __construct(?int $leadId = null)
    {
        $message = 'Lead has already been converted to an opportunity';
        if ($leadId) {
            $message .= " (Lead ID: {$leadId})";
        }

        parent::__construct($message);
    }
}
