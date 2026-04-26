<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Strategies\ReportGeneration;

interface ReportGenerationStrategyInterface
{
    public function supports(string $type): bool;

    /**
     * @return array<string, mixed>
     */
    public function generate(string $type, array $params = []): array;
}
