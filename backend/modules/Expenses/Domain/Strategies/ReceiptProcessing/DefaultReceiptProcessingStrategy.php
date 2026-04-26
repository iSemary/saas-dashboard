<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Strategies\ReceiptProcessing;

class DefaultReceiptProcessingStrategy implements ReceiptProcessingStrategyInterface
{
    public function process(array $fileData): array
    {
        // Stub: in future, OCR processing would extract amount, date, vendor
        return [
            'extracted_amount' => null,
            'extracted_date' => null,
            'extracted_vendor' => null,
            'confidence' => 0,
        ];
    }
}
