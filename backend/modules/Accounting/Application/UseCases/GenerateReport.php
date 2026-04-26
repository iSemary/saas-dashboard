<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases;

use Modules\Accounting\Infrastructure\Persistence\ChartOfAccountRepositoryInterface;

class GenerateReport
{
    public function __construct(
        private readonly ChartOfAccountRepositoryInterface $repository,
    ) {}

    use Modules\Accounting\Domain\Strategies\ReportGeneration\ReportGenerationStrategyInterface;

    public function __construct(
        private readonly ChartOfAccountRepositoryInterface \$repository,
        private readonly ReportGenerationStrategyInterface \$reportStrategy,
    ) {}

    public function execute(string \$type, array \$params = []): array
    {
        return \$this->reportStrategy->generate(\$type, \$params);
    }

}
