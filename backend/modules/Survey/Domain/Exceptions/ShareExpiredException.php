<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Exceptions;

use RuntimeException;

class ShareExpiredException extends RuntimeException
{
    public function __construct(string $token)
    {
        parent::__construct(
            "Survey share link has expired or is no longer valid",
            410
        );
    }
}
