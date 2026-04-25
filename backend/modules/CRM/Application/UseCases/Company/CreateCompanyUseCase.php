<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Company;

use Modules\CRM\Domain\Entities\Company;
use Modules\CRM\Domain\Events\CompanyCreated;
use Modules\CRM\Infrastructure\Persistence\CompanyRepositoryInterface;

class CreateCompanyUseCase
{
    public function __construct(private readonly CompanyRepositoryInterface $companies) {}

    public function execute(array $data, int $userId): Company
    {
        $data['created_by'] = $userId;
        $company = $this->companies->create($data);
        event(new CompanyCreated($company, $data));
        return $company;
    }
}
