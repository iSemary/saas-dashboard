<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CRM\Domain\Entities\Company;

class CompanyCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Company $company, public readonly array $data = []) {}
}
