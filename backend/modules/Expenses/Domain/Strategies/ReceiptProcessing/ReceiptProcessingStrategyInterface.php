<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Strategies\ReceiptProcessing;

interface ReceiptProcessingStrategyInterface
{
    public function process(array $fileData): array;
}
