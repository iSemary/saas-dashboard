<?php

declare(strict_types=1);

namespace Modules\Accounting\Application\UseCases\Report;

use Modules\Accounting\Domain\Strategies\ReportGeneration\ReportGenerationStrategyInterface;

class GenerateReportUseCase
{
    public function __construct(
        private readonly ReportGenerationStrategyInterface $reportStrategy,
    ) {}

    public function execute(string $type, array $params = []): array
    {
        if (! $this->reportStrategy->supports($type)) {
            throw new \InvalidArgumentException("Unsupported report type: {$type}");
        }

        return $this->reportStrategy->generate($type, $params);
    }
}
